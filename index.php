<?php

@include_once __DIR__ . '/vendor/autoload.php';

load([
     'Thathoff\\Oauth\\Controller' => 'lib/Controller.php',
     'Thathoff\\Oauth\\OauthMethod' => 'lib/OauthMethod.php',
     'Thathoff\\Oauth\\ProvidersManager' => 'lib/ProvidersManager.php',
     'Thathoff\\Oauth\\Provider' => 'lib/Provider.php',
], __DIR__);

Kirby::plugin('thathoff/oauth', [
    // one login method per provider, e.g. `oauth-google`
    'authMethods' => Thathoff\Oauth\OauthMethod::types(option('thathoff.oauth.providers')),
    'routes' => include(__DIR__ . '/plugin/routes.php'),
    'options' => include(__DIR__ . '/plugin/options.php'),
    'api' => include(__DIR__ . '/plugin/api.php'),
    'translations' => include(__DIR__ . '/plugin/translations.php'),
]);
