<?php

namespace Thathoff\Oauth;

use Kirby\Cms\User;
use Kirby\Http\Header;
use Kirby\Http\Uri;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class Controller
{
    private $kirby = null;
    private $providers = null;
    private $session = null;

    public function __construct()
    {
        $this->kirby = kirby();
        $this->session = $this->kirby->session();
        $this->providers = new ProvidersManager($this->kirby);
    }

    private function providers()
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

    public function settings()
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

    public function login($provider = null)
    {
        if (!$provider = $this->providers->get($provider)) {
            $this->error("Oauth Provider not found!");
        }

        // Got an error, probably user denied access
        if (get('error')) {
            $this->error(get('error'));
        }

        // If we don't have an authorization code then get one
        if (!$code = get('code')) {
            $authorizationUrl = $provider->getAuthorizationUrl();
            $this->session->set('oauth2state', $provider->getState());

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;
        }

        // we already have a user just go to panel
        if ($this->kirby->user()) {
            $this->goToPanel();
        }

        // State is invalid, possible CSRF attack in progress
        if (empty(get('state')) || (get('state') !== $this->session->get('oauth2state'))) {
            $this->session->remove('oauth2state');
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

    public function oauthError()
    {
        $error = $this->session->get('oauthError');
        $this->session->remove('oauthError');

        return [
            'msg' => $error
        ];
    }

    public static function handle($options)
    {
        $options = explode("/", trim($options, "/"));
        $method = null;

        if (!empty($options[0])) {
            $method = array_shift($options);
        }

        $instance = new Controller();
        if (method_exists($instance, $method)) {
            return call_user_func_array([$instance, $method], $options);
        }

        Header::notfound();
        return "Not found!";
    }

    private function loginUser(ResourceOwnerInterface $oauthUser, Provider $provider)
    {
        $oauthUserData = $oauthUser->toArray();

        $vars = ['name', 'email_verified', 'hd'];

        foreach ($vars as $var) {
            $$var = isset($oauthUserData[$var]) ? $oauthUserData[$var] : null;
        }

        // The email field name can be configured per provider (defaults to "email").
        // For example, Azure Active Directory uses "upn" (User Principal Name) instead.
        $emailField = $provider->getEmailField();
        $email = isset($oauthUserData[$emailField]) ? $oauthUserData[$emailField] : null;

        // A provider may also be configured to always be treated as verifying emails
        // (e.g. Azure AD tenants), which overrides the email_verified claim.
        $providerEmailVerified = $provider->getEmailVerified();
        if ($providerEmailVerified !== null) {
            $email_verified = $providerEmailVerified;
        }

        if (!$email) {
            $this->error("E-mail address missing!");
        }

        // if email is not verified and check is not disabled abort login
        $skipEmailVerifiedCheck = $this->kirby->option('thathoff.oauth.skipEmailVerifiedCheck', false);
        if ($skipEmailVerifiedCheck === false && $email_verified !== true) {
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
                $kirbyUser = $this->kirby->impersonate('kirby', function() use ($name, $email, $role) {
                    $userData = [
                        'name'      => $name,
                        'email'     => $email,
                        'role'      => $role,
                    ];

                    // The first user requires a password to be set
                    // all other users can be created without a password
                    if (!$this->kirby->users()->length() > 0) {
                        $userData['password'] = bin2hex(random_bytes(32));
                    }

                    return $this->kirby->users()->create($userData);
                });
            }

            $this->kirby->trigger('thathoff.oauth.user-create:after', ['oauthUser' => $oauthUser, 'user' => $kirbyUser]);

            if(!$kirbyUser) {
                $this->error("User cannot be created.");
            }
        }

        $this->kirby->trigger('thathoff.oauth.login:before', ['oauthUser' => $oauthUser, 'user' => $kirbyUser]);
        $kirbyUser->loginPasswordless();
        $this->kirby->trigger('thathoff.oauth.login:after', ['oauthUser' => $oauthUser, 'user' => $kirbyUser,]);

        $this->goToPanel();
    }

    private function checkWhiteLists($email)
    {
        $domainWhitelist = $this->kirby->option('thathoff.oauth.domainWhitelist', []);
        $emailWhitelist = $this->kirby->option('thathoff.oauth.emailWhitelist', []);
        $allowEveryone = $this->kirby->option('thathoff.oauth.allowEveryone', false);

        if ($allowEveryone) {
            return true;
        }

        if (is_array($emailWhitelist) && in_array($email, $emailWhitelist)) {
            return true;
        }

        $domain = substr($email, strpos($email, "@") + 1);
        if (is_array($domainWhitelist) && in_array($domain, $domainWhitelist)) {
            return true;
        }

        return false;
    }

    private function error($message = null)
    {
        $this->session->set("oauthError", $message);
        go($this->kirby->url('panel') . '/login');
    }

    private function goToPanel()
    {
        go($this->kirby->url('panel'));
    }
}
