# Changelog

## [4.0.0](https://github.com/thathoff/kirby-oauth/compare/v3.6.0...v4.0.0) (2026-11-25)


### ⚠ BREAKING CHANGES

* require panel.install or allowInstallation to create the first user
* rename whitelist options to allowlist
* newly created OAuth users now get the `editor` role instead of `admin` when `defaultRole` is not set. Set `'defaultRole' => 'admin'` to keep the previous behaviour.
* requires Kirby 6. OAuth is now a regular auth method that has to be enabled via `auth.methods` (e.g. ['password', 'oauth']). The `onlyOauth` option and the `oauth/settings` and `oauth/oauthError` API endpoints have been removed. OAuth logins now trigger Kirby's second factor if the user has set one up.

### Features

* add label and icon options for the oauth login method ([27b34b6](https://github.com/thathoff/kirby-oauth/commit/27b34b6edf9776fa7e003fa9ff9d39d7b0646ec2))
* allow disabling kirby's second factor for oauth logins ([fcf217f](https://github.com/thathoff/kirby-oauth/commit/fcf217fd6bbf7834ed5cc13a270b46bb4c5d73bd))
* auto redirect when the oauth method is selected in the login method switcher ([9f3832d](https://github.com/thathoff/kirby-oauth/commit/9f3832d971b2ebdb36dc0752d6ff965cfbb04b46))
* change default role for new users to editor ([530a7f4](https://github.com/thathoff/kirby-oauth/commit/530a7f401eb96d5238de405b9c69aac3da6b19e4)), closes [#59](https://github.com/thathoff/kirby-oauth/issues/59)
* migrate to kirby 6 auth methods ([e59767e](https://github.com/thathoff/kirby-oauth/commit/e59767ed70ccd58e9049642d681148289e8d8280))
* rename whitelist options to allowlist ([8d0b775](https://github.com/thathoff/kirby-oauth/commit/8d0b77529130238c7948e42dec88e4082a510add))
* require panel.install or allowInstallation to create the first user ([4f0c6c9](https://github.com/thathoff/kirby-oauth/commit/4f0c6c971f6446afafd8a63957f7332c9eb1d5a0))
* show the provider name in the login method label and auto login message ([8c5666a](https://github.com/thathoff/kirby-oauth/commit/8c5666a40ff1a769e8c8fb435bee1e571fb327dd))
* trigger the login:after hook once the second factor is confirmed ([99c97b8](https://github.com/thathoff/kirby-oauth/commit/99c97b8063fe63dff76a0bb5800f3c2ae2d137c5))
* use provider name and icon for the login method with a single provider ([3d9d257](https://github.com/thathoff/kirby-oauth/commit/3d9d25728706c5fedaae2d113ad8797775963dfd))


### Refactoring

* add single provider helpers to the providers manager ([8dda0c5](https://github.com/thathoff/kirby-oauth/commit/8dda0c5f1abccdd0bf7fb8746b7459f06bb2af15))


### Documentation

* shorten the azure ad upn note in the upgrade guide ([d73326e](https://github.com/thathoff/kirby-oauth/commit/d73326ef8fa91c0bca7f32187892a57af840f492))
* update screenshot with new login layout ([357df43](https://github.com/thathoff/kirby-oauth/commit/357df437594d9455cb6434365b2ca8aea04fd8fa))


### Build System

* declare kirby 6 compatibility ([23715ec](https://github.com/thathoff/kirby-oauth/commit/23715ecff00ff2a8ac31da4d1adead3fe3952cb0))
* ignore package-lock.json ([7f78bac](https://github.com/thathoff/kirby-oauth/commit/7f78bac5aab8c22e6a2fecb9b97e101de8801236))


### CI

* run checks against php 8.6 ([f9c1734](https://github.com/thathoff/kirby-oauth/commit/f9c1734431042941c372dcb621c71afd6c6091d5))

## [3.6.0](https://github.com/thathoff/kirby-oauth/compare/v3.5.0...v3.6.0) (2026-10-04)


### Features

* make email field and verification configurable per provider ([9bbd932](https://github.com/thathoff/kirby-oauth/commit/9bbd9320ee80102337781857f75f623c408d08a2))


### Bug Fixes

* prevent calling private controller methods via routes ([b6d311b](https://github.com/thathoff/kirby-oauth/commit/b6d311ba4d9a5b5295fd55d45d571368347ce8a7))
* reject login when email_verified claim is missing ([a0f61e2](https://github.com/thathoff/kirby-oauth/commit/a0f61e22fd49cb20fcafff6469561694f47da030))
* use strict comparison and case-insensitive whitelist checks ([ca8d6bd](https://github.com/thathoff/kirby-oauth/commit/ca8d6bd74c33a76a9554c9c99f40b4b6ab8b8dbe))


### Refactoring

* migrate to phpstan level 6 ([c4d25a7](https://github.com/thathoff/kirby-oauth/commit/c4d25a7a8916c330d74df3d21865ae4a5db65b20))
* migrate to phpstan level 7 ([832d1b4](https://github.com/thathoff/kirby-oauth/commit/832d1b4f72e011f1cfd9a664eede3cad5b4dc8b9))
* migrate to phpstan level 8 ([80cf476](https://github.com/thathoff/kirby-oauth/commit/80cf476d96fc01f28cf502da29d74a4589675906))
* replace dynamic variable assignment in loginUser ([8b0bec3](https://github.com/thathoff/kirby-oauth/commit/8b0bec3c8628b94b87bc62ac9613d738104dfd6a))


### CI

* add github action for phpstan and phpcs ([ffb46d9](https://github.com/thathoff/kirby-oauth/commit/ffb46d9b6e84de5bcd1bf8da72ab899e243a0aa6))
* add release-please workflow for automated releases ([192952c](https://github.com/thathoff/kirby-oauth/commit/192952cb7b66a10b4c67fde60b944302464e63ee))
* run checks against kirby 4 and 5 ([cde5286](https://github.com/thathoff/kirby-oauth/commit/cde52867aa3db2bedaad6084a5e1852fd0943b69))
