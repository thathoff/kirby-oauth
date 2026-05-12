<?php

namespace Thathoff\Oauth;

use Kirby\Cms\App;
use Kirby\Cms\User;
use Kirby\Http\Header;
use Kirby\Http\Uri;
use Kirby\Session\Session;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class Controller
{
    private App $kirby;
    private ProvidersManager $providers;
    private Session $session;

    public function __construct()
    {
        $this->kirby = kirby();
        $this->session = $this->kirby->session();
        $this->providers = new ProvidersManager($this->kirby);
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    private function providers(): array
    {
        return $this->providers->count() > 0 ?
            $this->providers->toArray(
                function ($provider) {
                    return [
                        'id'   => $provider->getId(),
                        'name' => $provider->getName(),
                        'href' => new Uri('oauth/login') . '/' . $provider->getId(),
                        'icon' => $provider->getIcon(),
                        'theme' => $provider->getTheme(),
                    ];
                }
            )
            : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        $onlyOauth = $this->kirby->option('thathoff.oauth.onlyOauth', false);
        $autoRedirect = $onlyOauth && $this->kirby->option('thathoff.oauth.autoRedirect', false);

        return [
            'onlyOauth' => $onlyOauth,
            'autoRedirect' => $autoRedirect,
            'enabled' => count($this->providers) > 0,
            'providers' => $this->providers(),
        ];
    }

    public function login(?string $provider = null): void
    {
        if ($provider === null) {
            $this->error("Oauth Provider not found!");
        }

        $provider = $this->providers->get($provider);
        if (!$provider instanceof Provider) {
            $this->error("Oauth Provider not found!");
        }

        // Got an error, probably user denied access
        $error = get('error');
        if ($error) {
            $this->error(is_string($error) ? $error : 'Unknown error');
        }

        // If we don't have an authorization code then get one
        if (!$code = get('code')) {
            $authorizationUrl = $provider->getAuthorizationUrl();
            $this->session->data()->set('oauth2state', $provider->getState());

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;
        }

        // we already have a user just go to panel
        if ($this->kirby->user()) {
            $this->goToPanel();
        }

        // State is invalid, possible CSRF attack in progress
        if (empty(get('state')) || (get('state') !== $this->session->data()->get('oauth2state'))) {
            $this->session->data()->remove('oauth2state');
            $this->error('Invalid state');
        }

        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            // We got an access token, let's now get the owner details
            $ownerDetails = $provider->getResourceOwner($token);

            // Use these details to login
            $this->loginUser($ownerDetails, $provider);
        } catch (\Exception $e) {
            // Failed to get user details
            $this->error($e->getMessage());
        }

        $this->error();
    }

    /**
     * @return array<string, mixed>
     */
    public function oauthError(): array
    {
        $error = $this->session->data()->get('oauthError');
        $this->session->data()->remove('oauthError');

        return [
            'msg' => $error
        ];
    }

    public static function handle(string $options): mixed
    {
        $options = explode("/", trim($options, "/"));
        $method = array_shift($options);

        if ($method !== '') {
            $instance = new Controller();
            if (method_exists($instance, $method)) {
                return $instance->$method(...$options);
            }
        }

        Header::notfound();
        return "Not found!";
    }

    private function loginUser(ResourceOwnerInterface $oauthUser, Provider $provider): void
    {
        $oauthUserData = $oauthUser->toArray();

        $name = $oauthUserData['name'] ?? null;
        $emailVerified = $oauthUserData['email_verified'] ?? null;

        // The email field name can be configured per provider (defaults to "email").
        // For example, Azure Active Directory uses "upn" (User Principal Name) instead.
        $emailField = $provider->getEmailField();
        $email = $oauthUserData[$emailField] ?? null;

        // A provider may also be configured to always be treated as verifying emails
        // (e.g. Azure AD tenants), which overrides the email_verified claim.
        $providerEmailVerified = $provider->getEmailVerified();
        if ($providerEmailVerified !== null) {
            $emailVerified = $providerEmailVerified;
        }

        if (!$email) {
            $this->error("E-mail address missing!");
        }

        // if email is not verified and check is not disabled abort login
        $skipEmailVerifiedCheck = $this->kirby->option('thathoff.oauth.skipEmailVerifiedCheck', false);
        if ($skipEmailVerifiedCheck === false && $emailVerified !== true) {
            $this->error("E-mail address not verified!");
        }

        if (!$kirbyUser = $this->kirby->user($email)) {
            $createResult = $this->kirby->apply('thathoff.oauth.user-create:before', ['oauthUser' => $oauthUser, 'result' => null], 'result');
            $kirbyUser = null;

            if ($createResult instanceof User) {
                $kirbyUser = $createResult;
            }

            if ($createResult === true || $createResult === null) {
                $onlyExistingUsers = $this->kirby->option('thathoff.oauth.onlyExistingUsers', false);
                $defaultRole = $this->kirby->option('thathoff.oauth.defaultRole', 'admin');
                $admins = $this->kirby->option('thathoff.oauth.adminWhitelist', []);

                if ($onlyExistingUsers) {
                    $this->error("User missing and creating users is disabled!");
                }

                if (!$this->checkWhiteLists($email)) {
                    $this->error("Access denied for $email.");
                }

                // Normalize values to be Case-Insensitive
                $adminsNormalized = A::map($admins, fn($value) => Str::lower($value));
                $emailNormalized = Str::lower($email);
                $role = (!empty($admins) && A::has($adminsNormalized, $emailNormalized)) ? 'admin' : $defaultRole;

                // Create User
                $kirbyUser = $this->kirby->impersonate('kirby', function () use ($name, $email, $role) {
                    $userData = [
                        'name'      => $name,
                        'email'     => $email,
                        'role'      => $role,
                    ];

                    // The first user requires a password to be set
                    // all other users can be created without a password
                    if ($this->kirby->users()->count() === 0) {
                        $userData['password'] = bin2hex(random_bytes(32));
                    }

                    return $this->kirby->users()->create($userData);
                });
            }

            $this->kirby->trigger('thathoff.oauth.user-create:after', ['oauthUser' => $oauthUser, 'user' => $kirbyUser]);

            if (!$kirbyUser) {
                $this->error("User cannot be created.");
            }
        }

        $this->kirby->trigger('thathoff.oauth.login:before', ['oauthUser' => $oauthUser, 'user' => $kirbyUser]);
        $kirbyUser->loginPasswordless();
        $this->kirby->trigger('thathoff.oauth.login:after', ['oauthUser' => $oauthUser, 'user' => $kirbyUser,]);

        $this->goToPanel();
    }

    private function checkWhiteLists(string $email): bool
    {
        $domainWhitelist = $this->kirby->option('thathoff.oauth.domainWhitelist', []);
        $emailWhitelist = $this->kirby->option('thathoff.oauth.emailWhitelist', []);
        $allowEveryone = $this->kirby->option('thathoff.oauth.allowEveryone', false);

        if ($allowEveryone) {
            return true;
        }

        $emailNormalized = Str::lower($email);

        if (is_array($emailWhitelist)) {
            $emailWhitelistNormalized = A::map($emailWhitelist, fn($value) => Str::lower($value));
            if (in_array($emailNormalized, $emailWhitelistNormalized, true)) {
                return true;
            }
        }

        $domain = Str::lower(substr($email, strpos($email, "@") + 1));
        if (is_array($domainWhitelist)) {
            $domainWhitelistNormalized = A::map($domainWhitelist, fn($value) => Str::lower($value));
            if (in_array($domain, $domainWhitelistNormalized, true)) {
                return true;
            }
        }

        return false;
    }

    private function error(?string $message = null): never
    {
        $this->session->data()->set("oauthError", $message);
        go($this->kirby->url('panel') . '/login');
    }

    private function goToPanel(): never
    {
        go((string)$this->kirby->url('panel'));
    }
}
