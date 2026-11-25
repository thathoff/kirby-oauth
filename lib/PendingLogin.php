<?php

namespace Thathoff\Oauth;

use Kirby\Cms\App;
use Kirby\Cms\User;
use Kirby\Session\Session;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Remembers the OAuth user in the session while a
 * second factor is pending, so that the
 * `thathoff.oauth.login:after` hook can be triggered
 * once the user has confirmed Kirby's challenge
 *
 * The pending login is dropped as soon as another login
 * is started or the user logs out (see `plugin/hooks.php`),
 * so that it cannot be picked up by a login that did not
 * go through OAuth. It does not need an expiry of its own:
 * it is only used while Kirby verifies the challenge,
 * which is limited by the challenge timeout.
 */
class PendingLogin
{
    public const SESSION_KEY = 'thathoff.oauth.pendingLogin';

    public static function remember(Session $session, User $user, ResourceOwnerInterface $oauthUser): void
    {
        // objects cannot be stored in the session,
        // so only the data of the OAuth user is kept
        $session->set(static::SESSION_KEY, [
            'user' => $user->id(),
            'id'   => $oauthUser->getId(),
            'data' => $oauthUser->toArray(),
        ]);
    }

    public static function forget(Session $session): void
    {
        // only touch the session if there is something to remove
        if ($session->get(static::SESSION_KEY) !== null) {
            $session->remove(static::SESSION_KEY);
        }
    }

    /**
     * Returns the remembered OAuth user if it belongs to
     * the given user; the pending login is removed from
     * the session in any case
     */
    public static function pull(Session $session, User $user): ?ResourceOwnerInterface
    {
        $pending = $session->get(static::SESSION_KEY);

        if ($pending === null) {
            return null;
        }

        $session->remove(static::SESSION_KEY);

        if (
            !is_array($pending) ||
            ($pending['user'] ?? null) !== $user->id() ||
            !is_array($pending['data'] ?? null)
        ) {
            return null;
        }

        return new SessionResourceOwner($pending['id'] ?? null, $pending['data']);
    }

    /**
     * Checks whether the request verifies a login challenge
     * (Panel login view or REST API)
     */
    public static function isChallengeRequest(App $kirby): bool
    {
        $request = $kirby->request();

        return
            $request->method() === 'POST' &&
            preg_match(
                '!(^|/)(login/challenge(/[^/]+)?|auth/code)$!',
                $request->path()->toString()
            ) === 1;
    }

    /**
     * Checks whether the route starts another login
     * or logs the user out (Panel or REST API)
     */
    public static function isLoginOrLogoutRoute(string $path, string $method): bool
    {
        if ($method === 'POST' && preg_match('!(^|/)(login/method/[^/]+|auth/login|auth/logout)$!', $path) === 1) {
            return true;
        }

        return preg_match('!(^|/)logout$!', $path) === 1;
    }
}
