# Changelog

Notable changes to `crmleaf/savings-calculator`.

Format per [Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versioning
per [Semantic Versioning](https://semver.org/spec/v2.0.0.html) - with one extra
rule this package observes, because it computes statutory figures:

> **Any change that alters a published result is at minimum a minor release**,
> and is listed under `Changed` with the notification, circular or Act section
> that prompted it.

## [Unreleased]

## [1.0.0] - 2026-08-12

### Added

- Initial release. Compares what you pay now with what you would pay instead, over the contract you would actually sign, with the migration cost counted rather than waved away.

### Statutory basis

- Not a statutory calculation. It compares two per-employee-per-month prices over a contract term, including platform fees and the one-off cost of migrating, so the answer is a total cost of ownership rather than a headline rate.

[Unreleased]: https://github.com/crmleaf/savings-calculator/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/crmleaf/savings-calculator/releases/tag/v1.0.0
