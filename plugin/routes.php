<?php

namespace Thathoff\Oauth;

use Kirby\Http\Response;

return [
    [
        'pattern' => 'oauth(:all)',
        'action'  => function ($option) {
            $controller = new Controller();

            // parse the option into method and parameters
            $options = explode("/", trim($option, "/"));
            $method  = array_shift($options);

            // Check if the method exists and is callable (public)
            if (empty($method) || !is_callable([$controller, $method])) {
                return new Response('Not Found', 'text/plain', 404);
            }

            return call_user_func_array([$controller, $method], $options);
        },
    ],
];
