<?php

@include_once __DIR__ . '/vendor/autoload.php';

load([
     'Blankogmbh\\Oauth\\Controller' => 'src/Controller.php',
     'Blankogmbh\\Oauth\\ProvidersManager' => 'src/ProvidersManager.php',
     'Blankogmbh\\Oauth\\Provider' => 'src/Provider.php',
], __DIR__);

Kirby::plugin('blankogmbh/oauth', [
    'routes' => include(__DIR__ . "/plugin/routes.php"),
    'options' => include(__DIR__ . "/plugin/options.php"),
]);
