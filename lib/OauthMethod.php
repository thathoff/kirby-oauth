<?php

namespace Thathoff\Oauth;

use Kirby\Auth\Auth;
use Kirby\Auth\Method;
use Kirby\Auth\Status;
use Kirby\Cms\User;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Panel\Ui\Component;
use SensitiveParameter;

/**
 * Kirby auth method for OAuth 2.0 logins
 *
 * The login itself is redirect-based and handled by the
 * `oauth/login/(:any)` route (see Controller); this class
 * provides the Panel login form and finishes the login
 * (incl. an optional second factor) once the OAuth
 * provider has confirmed the user.
 */
class OauthMethod extends Method
{
    public const SESSION_ERROR = 'thathoff.oauth.error';

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
        $kirby     = $this->auth->kirby();
        $providers = (new ProvidersManager($kirby))->toPanel();
        $error     = $kirby->session()->pull(static::SESSION_ERROR);

        // only redirect automatically when there is no other way to log in
        $autoRedirect =
            $kirby->option('thathoff.oauth.autoRedirect', false) === true &&
            count($this->auth->methods()->enabled()) === 1 &&
            count($providers) === 1 &&
            empty($error) === true;

        return new Component(
            component:    'k-login-oauth-method-form',
            providers:    $providers,
            error:        is_string($error) ? $error : null,
            autoRedirect: $autoRedirect,
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

    public static function icon(): string
    {
        return 'users';
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function isEnabled(Auth $auth, array $options = []): bool
    {
        return (new ProvidersManager($auth->kirby()))->count() > 0;
    }
}
