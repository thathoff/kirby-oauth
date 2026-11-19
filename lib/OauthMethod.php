<?php

namespace Thathoff\Oauth;

use Kirby\Auth\Method;
use Kirby\Auth\Status;
use Kirby\Cms\User;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Panel\Ui\Component;
use Kirby\Toolkit\Str;
use SensitiveParameter;

/**
 * Kirby auth method for OAuth 2.0 logins
 *
 * The method is registered once per provider as `oauth-<id>`.
 * The login itself is redirect-based and handled by the
 * `oauth/login/(:any)` route (see Controller); this class
 * provides the Panel login form and finishes the login
 * (incl. an optional second factor) once the OAuth
 * provider has confirmed the user.
 */
class OauthMethod extends Method
{
    public const SESSION_ERROR = 'thathoff.oauth.error';
    public const TYPE_PREFIX = 'oauth-';

    private Provider|null|false $provider = false;

    /**
     * OAuth logins are not submitted via the Panel login form
     * but go through the provider redirect flow instead
     */
    public function authenticate(
        string|null $email,
        #[SensitiveParameter]
        string|null $password = null,
        bool $long = false
    ): User|Status {
        throw new InvalidArgumentException(
            message: 'Please use one of the OAuth provider buttons to log in'
        );
    }

    /**
     * Logs the user in after a successful OAuth authorization
     * or creates a 2FA challenge if a second factor is required
     */
    public function complete(User $user): User|Status
    {
        if ($this->has2FA($user) === true) {
            $email = $user->email();

            if ($email === null) {
                throw new InvalidArgumentException(
                    message: 'The user has no email address for the second factor'
                );
            }

            return $this->auth->createChallenge(
                email: $email,
                mode:  '2fa',
            );
        }

        $user->loginPasswordless(['createMode' => 'cookie']);

        return $user;
    }

    public function form(): Component
    {
        $kirby    = $this->auth->kirby();
        $provider = $this->provider();
        $error    = $kirby->session()->pull(static::SESSION_ERROR);
        $methods  = $this->auth->methods()->enabled();

        // choosing this method in the method switcher already is the click
        // on the provider button, so redirect right away; except for the
        // default method, otherwise opening the login view would redirect,
        // unless `autoRedirect` is enabled and there is no other way to log in
        $redirect =
            empty($error) === true &&
            (
                array_key_first($methods) !== $this->type() ||
                (
                    $kirby->option('thathoff.oauth.autoRedirect', false) === true &&
                    count($methods) === 1
                )
            );

        return new Component(
            component: 'k-login-oauth-method-form',
            provider:  $provider !== null ? ProvidersManager::toPanelItem($kirby, $provider) : null,
            error:     is_string($error) ? $error : null,
            redirect:  $redirect,
        );
    }

    /**
     * Checks whether a second factor is required for this user
     * (same logic as for Kirby's password method)
     */
    public function has2FA(User $user): bool
    {
        // enforced: every user needs a second factor,
        // even if they have not set one up yet
        if (($this->options['2fa'] ?? null) === true) {
            return true;
        }

        // otherwise only challenge users who have any
        // second factor set up (= available) for them
        return $this->auth->challenges()->hasAvailable($user, '2fa');
    }

    public function icon(): string
    {
        return $this->provider()?->getIcon() ?? 'users';
    }

    public function isEnabled(): bool
    {
        return $this->provider() !== null;
    }

    public function label(): string
    {
        return static::i18n('thathoff.oauth.loginWith', [
            'name' => $this->provider()?->getName() ?? $this->type()
        ]);
    }

    /**
     * Returns the OAuth provider of this method
     */
    public function provider(): Provider|null
    {
        if ($this->provider === false) {
            $id = Str::after($this->type(), static::TYPE_PREFIX);
            $provider = (new ProvidersManager($this->auth->kirby()))->get($id);
            $this->provider = $provider instanceof Provider ? $provider : null;
        }

        return $this->provider;
    }

    /**
     * Returns the method type for a provider id
     */
    public static function typeFor(string $provider): string
    {
        return static::TYPE_PREFIX . $provider;
    }

    /**
     * Returns the auth method types to register,
     * one for each configured provider
     *
     * @return array<string, class-string<static>>
     */
    public static function types(mixed $providers): array
    {
        $types = [];

        if (is_array($providers) === false) {
            return $types;
        }

        foreach (array_keys($providers) as $id) {
            // the id is used in URLs and as method type
            if (is_string($id) === true && preg_match('/^[a-z0-9_-]+$/i', $id) === 1) {
                $types[static::typeFor($id)] = static::class;
            }
        }

        return $types;
    }
}
