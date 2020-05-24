<?php

namespace Thathoff\Oauth;

return [
    [
        'pattern' => 'oauth(:all)',
        'action'  => function ($option) {
            return Controller::handle($option);
        },
    ],
];
