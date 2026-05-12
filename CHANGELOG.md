# Changelog

## [3.6.0](https://github.com/thathoff/kirby-oauth/compare/v3.5.0...v3.6.0) (2026-05-12)


### Features

* make email field and verification configurable per provider ([9bbd932](https://github.com/thathoff/kirby-oauth/commit/9bbd9320ee80102337781857f75f623c408d08a2))


### Bug Fixes

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
