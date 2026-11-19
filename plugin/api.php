<?php

namespace Thathoff\Oauth;

return [
    'routes' => [
        [
            // provider buttons for the installation view;
            // only available as long as no user exists
            'pattern' => 'oauth/installation',
            'auth'    => false,
            'method'  => 'GET',
            'action'  => function () {
                $kirby = kirby();

                if ($kirby->users()->count() > 0) {
                    return ['providers' => [], 'error' => null];
                }

                $error = $kirby->session()->pull(OauthMethod::SESSION_ERROR);

                return [
                    'providers' => (new ProvidersManager($kirby))->toPanel(),
                    'error'     => is_string($error) ? $error : null,
                ];
            },
        ]
    ]
];
