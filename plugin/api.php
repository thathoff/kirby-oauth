<?php

namespace Thathoff\Oauth;

return [
    'routes' => [
        [
            // provider buttons for the installation view;
            // only available as long as no user exists, the
            // `oauth` method is enabled and installing is allowed
            'pattern' => 'oauth/installation',
            'auth'    => false,
            'method'  => 'GET',
            'action'  => function () {
                $kirby = kirby();

                // always consume the error, otherwise it would
                // show up on some later login
                $error = $kirby->session()->pull(OauthMethod::SESSION_ERROR);

                if (
                    $kirby->users()->count() > 0 ||
                    $kirby->auth()->methods()->has('oauth') === false ||
                    OauthMethod::isInstallable($kirby) === false
                ) {
                    return ['providers' => [], 'error' => null];
                }

                return [
                    'providers' => (new ProvidersManager($kirby))->toPanel(),
                    'error'     => is_string($error) ? $error : null,
                ];
            },
        ]
    ]
];
