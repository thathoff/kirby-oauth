<?php

namespace Thathoff\Oauth;

use Kirby\Cms\User;
use Kirby\Session\Session;
use Throwable;

return [
    // drops a pending OAuth login as soon as another login
    // is started or the user logs out
    'route:before' => function (string $path, string $method) {
        if (PendingLogin::isLoginOrLogoutRoute($path, $method) === false) {
            return;
        }

        // don't start a session just to clean it up
        if ($session = kirby()->sessions()->current()) {
            PendingLogin::forget($session);
        }
    },

    // finishes an OAuth login that required a second factor
    'user.login:after' => function (User $user, Session $session) {
        $kirby = $user->kirby();

        // only a confirmed challenge finishes a pending OAuth login,
        // any other login just drops leftovers
        if (PendingLogin::isChallengeRequest($kirby) === false) {
            PendingLogin::forget($session);
            return;
        }

        if ($oauthUser = PendingLogin::pull($session, $user)) {
            // this runs inside Kirby's challenge verification, which would
            // report any error as an invalid code and track it as a failed
            // attempt although the user is already logged in at this point
            try {
                $kirby->trigger('thathoff.oauth.login:after', ['oauthUser' => $oauthUser, 'user' => $user]);
            } catch (Throwable $e) {
                error_log('thathoff.oauth.login:after hook failed: ' . $e->getMessage());
            }
        }
    },

    'user.logout:before' => function (Session $session) {
        PendingLogin::forget($session);
    },
];
