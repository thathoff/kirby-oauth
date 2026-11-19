<?php

namespace Thathoff\Oauth;

return [
    [
        'pattern' => 'oauth/login/(:any)',
        'action'  => function (string $provider) {
            (new Controller())->login($provider);
        },
    ],
];
