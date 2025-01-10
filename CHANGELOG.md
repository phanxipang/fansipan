# Changelog

All notable changes to `fansipan` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

<!-- ## NEXT - YYYY-MM-DD

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing -->
## 1.3.0 - 2025-01-10

### What's Changed

#### Added

* PHP 8.4 test by @jenky in https://github.com/phanxipang/fansipan/pull/31

#### Fixed

* Throw exception when using `$response->object()` when decoder is not a mapper

#### Deprecated

* Add deprecation warning  when `$response->object()` return `null`

**Full Changelog**: https://github.com/phanxipang/fansipan/compare/1.2.0...1.3.0

## 1.2.0 - 2024-10-01

### What's Changed

#### Added

* Authenticator by @jenky in https://github.com/phanxipang/fansipan/pull/30
* Minor generics improvements

#### Deprecated

* Deprecate `BearerAuthentication` and `BasicAuthentication` in favor of [Authenticator](https://phanxipang.github.io/fansipan/advanced/authentication/)

**Full Changelog**: https://github.com/phanxipang/fansipan/compare/1.1.0...1.2.0

## 1.1.0 - 2024-07-11

### What's Changed

#### Added

* feat: update generics by @jenky in https://github.com/phanxipang/fansipan/pull/28
* Anonymous connectorless request by @jenky in https://github.com/phanxipang/fansipan/pull/29

**Full Changelog**: https://github.com/phanxipang/fansipan/compare/1.0.2...1.1.0

## 1.0.2 - 2024-03-05

### What's Changed

#### Added

* Make `GenericConnector` PSR-18 client.

**Full Changelog**: https://github.com/phanxipang/fansipan/compare/1.0.1...1.0.2

## 1.0.1 - 2024-02-21

### What's Changed

#### Added

* Quickly configure middleware by @jenky in https://github.com/phanxipang/fansipan/pull/27

**Full Changelog**: https://github.com/phanxipang/fansipan/compare/1.0.0...1.0.1

## 1.0.0 - 2024-02-02

### What's Changed

#### v1.0.0 stable release.

This release does not contain any changes but mark the project as stable. While this project becomes stable, [Mist](https://github.com/phanxipang/mist) start its journey in alpha stage.

**Full Changelog**: https://github.com/phanxipang/fansipan/compare/0.8.1...1.0.0

## 0.8.1 - 2024-01-05

### What's Changed

#### Fixed

* Handle undecodable responses gracefully by @jenky in https://github.com/phanxipang/fansipan/pull/25
* Fixes generics

**Full Changelog**: https://github.com/phanxipang/fansipan/compare/0.8.0...0.8.1

## 0.8.0 - 2024-01-05

### What's Changed

This release can be considered as `1.0-RC1` since there will be no changes to the public API.

#### Added

* Refactor decoder by @jenky in https://github.com/phanxipang/fansipan/pull/24

**Full Changelog**: https://github.com/phanxipang/fansipan/compare/0.7.0...0.8.0

## 0.7.0 - 2023-12-31

### What's Changed

#### Fixed

* Missing `code` by @jdecode in https://github.com/phanxipang/fansipan/pull/20
* Update doc by @jenky in https://github.com/phanxipang/fansipan/pull/22
* Refactor configurator by @jenky in https://github.com/phanxipang/fansipan/pull/23

### New Contributors

* @jdecode made their first contribution in https://github.com/phanxipang/fansipan/pull/20

**Full Changelog**: https://github.com/phanxipang/fansipan/compare/0.6.0...0.7.0

## 0.6.0 - 2023-10-27

### What's Changed

- Move to organization by @jenky in https://github.com/phanxipang/fansipan/pull/19

**Full Changelog**: https://github.com/phanxipang/fansipan/compare/0.5.2...0.6.0

## 0.5.2 - 2023-09-18

### Added

- Default `RequestException`

### Fixed

- Use global functions by @jenky in https://github.com/jenky/atlas/pull/17

### Removed

- `RequestAwareException` now replace by `RequestException`

**Full Changelog**: https://github.com/jenky/atlas/compare/0.5.1...0.5.2

## 0.5.1 - 2023-08-21

### Fixed

- Exception refactor
- Minor changes

**Full Changelog**: https://github.com/jenky/atlas/compare/0.5.0...0.5.1

## 0.5.0 - 2023-07-13

### Added

- Better way to create payload by @jenky in https://github.com/jenky/atlas/pull/14
- Use static function `baseUri` by @jenky in https://github.com/jenky/atlas/pull/15
- Support request protocol version by @jenky in https://github.com/jenky/atlas/pull/16
- Refactor exceptions
- Add ability to set request protocol version and method
- Minor improvements

**Full Changelog**: https://github.com/jenky/atlas/compare/0.4.0...0.5.0

## 0.4.0 - 2023-06-21

### What's Changed

#### Added

- Connector configurator by @jenky in https://github.com/jenky/atlas/pull/12
- Authentication middleware by @jenky in https://github.com/jenky/atlas/pull/13

**Full Changelog**: https://github.com/jenky/atlas/compare/0.3.0...0.4.0

## 0.3.0 - 2023-06-16

### Added

- Follow redirects by @jenky in https://github.com/jenky/atlas/pull/11

### Deprecated

- Remove `retry` method in favor of `RetryableConnector` for better DX

### Fixed

- Improve test cases performance by using mock client

**Full Changelog**: https://github.com/jenky/atlas/compare/0.2.0...0.3.0

## 0.2.0 - 2023-05-06

### Fixed

- Improve multi-part requests by @jenky in https://github.com/jenky/atlas/pull/10

**Full Changelog**: https://github.com/jenky/atlas/compare/0.1.0...0.2.0

## 0.1.0 - 2023-05-05

Initial release
