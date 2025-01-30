# Kirby OAuth 2.0 Plugin

![Screenshot of Kirby’s Login Screen with OAuth](/.github/screenshot.png?raw=true)

This plugin is an plugin to provide [OAuth 2.0](http://oauth.net/2/) support for panel authentication in [Kirby](https://getkirby.com). It uses the [PHP League’s OAuth 2 Client](https://oauth2-client.thephpleague.com/), so all [official](https://oauth2-client.thephpleague.com/providers/league/) and [third-party providers](https://oauth2-client.thephpleague.com/providers/thirdparty/) are supported. It’s even possible to [implement your own](https://oauth2-client.thephpleague.com/providers/implementing/).

## Kirby Compatibility

- For **Kirby 4** use version 3.0.0 or higher
- For **Kirby 3.6 - 3.9** use version 2 or higher
- For **Kirby 3.0 - 3.6** use version 1 (not maintained anymore)

---

## Installation with Composer

Because of secondary dependencies for providers, **installation via composer is the only currently supported method**.

### Install the Plugin

```sh
composer require thathoff/kirby-oauth
```

### Install a Provider

The Plugin uses [PHP League’s OAuth 2 Client](https://oauth2-client.thephpleague.com/) so you can select from all [official](https://oauth2-client.thephpleague.com/providers/league/) and [third-party providers](https://oauth2-client.thephpleague.com/providers/thirdparty/). It’s also possible to use your own provider by using the `GenericProvider` or [implement your own](https://oauth2-client.thephpleague.com/providers/implementing/) provider.

For example to install support for Google run:

```sh
composer require league/oauth2-google
```

## Options

### General Options

The following configuration options are available. And can be added to the Kirby `config.php`.

```php
return [
  //... other config options

  'thathoff' => [
      'oauth' => [
        // Add your providers configuration here
        'providers' => [
          // for details see „Provider Options” below
        ],

        // Only allow logins for existing kirby users (don’t create new users)
        'onlyExistingUsers' => false,

         // Set the default role of newly created users.
        'defaultRole' => 'admin',

        // Allow every valid user of all OAuth providers to login.
        // For details see “Configure Allowed Users” below.
        // DANGEROUS: Make sure you know what you’re doing when setting this to true!
        'allowEveryone' => false,

        // List of E-mail domains which are allowed to login
        'domainWhitelist' => [
          // For details see “Configure Allowed Users” below.
        ],

        // List of E-mail addresses which are allowed to login
        'emailWhitelist' => [
          // For details see “Configure Allowed Users” below.
        ],

        // Remove the standard Kirby login form and only display OAuth options.
        'onlyOauth' => false,
      ],
  ],
];
```

### Provider Options

The `thathoff.oauth.providers` array is a list of all configured OAuth Providers with a unique key for each entry. Each array entry is used as the configuration option to a new OAuth Provider Class instance so all options which are documented for the selected OAuth Provider class are available.

For example adding `'hostedDomain' => 'example.com'` in your google provider options will restrict users to an `@example.com` google account, as documented [here](https://github.com/thephpleague/oauth2-google).

Additionally the two properties `name` and `class` are supported to supply a display name for the login screen and the Provider class to use when you don’t want to use the `GenericProvider`.

```php
//...
'providers' => [
  'google' => [
    'class' => "League\OAuth2\Client\Provider\Google",  // Use special google class from league/oauth2-google
    'clientId' => 'somerandomstring.apps.googleusercontent.com',
    'clientSecret' => 'clientsecret',
    'hostedDomain' => 'example.com'  // Restrict users to an `@example.com` google account (optional)
    'icon'         => 'users'  // Pick any default Kirby icon for the login button (optional)
  ],
  'custom' => [
    // this one uses \League\OAuth2\Client\Provider\GenericProvider automatically
    'name'                    => 'My Custom Provider' // The name is optional
    'clientId'                => 'demoapp',    // The client ID assigned to you by the provider
    'clientSecret'            => 'demopass',   // The client password assigned to you by the provider
    'redirectUri'             => 'https://kirby.example.com/your-redirect-url/',
    'urlAuthorize'            => 'https://example.com/oauth2/lockdin/authorize',
    'urlAccessToken'          => 'https://example.com/oauth2/lockdin/token',
    'urlResourceOwnerDetails' => 'https://example.com/oauth2/lockdin/resource',
    'icon'                    => 'users',  // Pick any default Kirby icon for the login button (optional)
    'scope'                   => 'openid email profile'  //specify the scope passed form the OIDC provider to kirby
  ],
```

### Redirect URL

OAuth providers require you to supply a **redirect URL** of your kirby instance when configuring an application.
Please use `https://kirby.example.com/oauth/login/PROVIDER_ID` where kirby.example.com is your domain and PROVIDER_ID is the key
of the config option in config.php (in the previous config example `google` or `custom`). If you have
installed Kirby in a subdirectory, remember to include the subdirectory in the URL.

### Configure Allowed Users

By default only whitelisted users are allowed to login into the Kirby panel.

**Domain Whitelist:** By adding domains to the domain whitelist (`domainWhitelist`) all accounts with a verified email address at one of the domains are permitted.

**Email Whitelist:** By adding email addresses to the email whitelist (`emailWhitelist`) all accounts with a verified email address matching one of the entires are permitted.

**Allow Everyone:** By setting `allowEveryone` to `true` all authenticated accounts are able to login. *Please use this option with care!* You probably want to change the default user role to a more restricted one then the default `admin`.

**Default Role:** Newly created users get the role defined with `defaultRole` when they first login. The default is `admin`. Please note that when the user has ben created already the role will not be updated. You can set this role to `nobody` if you want to manually whitelist users by changing the role in the Kirby panel.

**Only Existing User:** By setting `onlyExistingUsers` to true only created uses are able to login with an OAuth provider, no new users are created.

### Use Hooks

Before and after the KirbyUser gets created or logged in a hook is triggered.

```php
/**
 * @var \League\OAuth2\Client\Provider\ResourceOwnerInterface $oauthUser
 * @var Kirby\Cms\User $user
 */

'hooks' => [
    'thathoff.oauth.user-create:before' => function ($oauthUser) {
      // return null|true to use the plugins user-creation
      // return a Kirby\Cms\User to overwrite the plugin user creation
    },
    'thathoff.oauth.user-create:after' => function ($oauthUser, $user) {

    },
    'thathoff.oauth.login:before' => function ($oauthUser, $user) {

    },
    'thathoff.oauth.login:after' => function ($oauthUser, $user) {

    }
]
```
