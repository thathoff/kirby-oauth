<?php

namespace Thathoff\Oauth;

return [
    [
        'pattern' => 'oauth/login/(:any)',
        'action'  => function ($provider) {
            return Controller::handle('login/' . $provider);
        },
    ],
];
