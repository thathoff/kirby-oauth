<?php

@include_once __DIR__ . '/vendor/autoload.php';

load([
     'Thathoff\\Oauth\\Controller' => 'src/Controller.php',
     'Thathoff\\Oauth\\ProvidersManager' => 'src/ProvidersManager.php',
     'Thathoff\\Oauth\\Provider' => 'src/Provider.php',
], __DIR__);

Kirby::plugin('thathoff/oauth', [
    'routes' => include(__DIR__ . "/plugin/routes.php"),
    'options' => include(__DIR__ . "/plugin/options.php"),
]);
