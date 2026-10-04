<?php

namespace Thathoff\Oauth;

return [
    'routes' => [
        [
            'pattern' => 'oauth/(settings|oauthError)',
            'auth'    => false,
            'method'  => 'GET',
            'action'  => function ($method) {
                $controller = new Controller();

                // See plugin/routes.php: the check has to live outside of
                // Controller, otherwise private methods pass as well.
                if (is_callable([$controller, $method])) {
                    return $controller->$method();
                }

                return false;
            },
        ]
    ]
];
