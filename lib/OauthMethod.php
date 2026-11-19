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

    protected ProvidersManager|null $providers = null;

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
        $kirby = $this->auth->kirby();
        $error = $kirby->session()->pull(static::SESSION_ERROR);

        // redirect automatically when the method has just been selected
        // in the login method switcher or, if enabled, when there is
        // no other way to log in
        $autoRedirect =
            $this->providers()->hasSingleProvider() === true &&
            empty($error) === true &&
            (
                $this->isSelectedInSwitcher() ||
                (
                    $kirby->option('thathoff.oauth.autoRedirect', false) === true &&
                    count($this->auth->methods()->enabled()) === 1
                )
            );

        return new Component(
            component:    'k-login-oauth-method-form',
            providers:    $this->providers()->toPanel(),
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

    public function icon(): string
    {
        if ($icon = $this->providers()->singleProvider()?->getIcon()) {
            return $icon;
        }

        $icon = $this->auth->kirby()->option('thathoff.oauth.icon');

        return is_string($icon) && $icon !== '' ? $icon : 'users';
    }

    /**
     * Uses “Login with <provider>” if only one provider is configured,
     * otherwise the `thathoff.oauth.label` option (string or
     * translations array) if set or the default label
     */
    public function label(): string
    {
        if ($provider = $this->providers()->singleProvider()) {
            return static::i18n('thathoff.oauth.loginWithProvider', [
                'provider' => $provider->getName()
            ]);
        }

        $label = $this->auth->kirby()->option('thathoff.oauth.label');

        if (empty($label) === false && (is_string($label) || is_array($label))) {
            return static::i18n($label);
        }

        return parent::label();
    }

    /**
     * Checks whether the login form is requested by the Panel
     * after a click in the login method switcher, as opposed to
     * a full page load, the redirect after a logout or after
     * the session expired while working in the Panel
     */
    protected function isSelectedInSwitcher(): bool
    {
        $panel = $this->auth->kirby()->panel();

        return
            $panel->isStateRequest() === true &&
            str_starts_with($panel->referrer(), '/login/method/') === true;
    }

    public function isEnabled(): bool
    {
        return $this->providers()->isNotEmpty();
    }

    protected function providers(): ProvidersManager
    {
        return $this->providers ??= new ProvidersManager($this->auth->kirby());
    }
}
