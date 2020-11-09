<?php

namespace Thathoff\Oauth;

return [
    'routes' => [
        [
            'pattern' => 'oauth/(settings|providers|oauthError)',
            'auth'    => false,
            'method'  => 'GET',
            'action'  => function ($options) {
                return Controller::handle($options);
            },
        ]
    ]
];
