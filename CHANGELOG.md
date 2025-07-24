# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.3.8] - 2025-07-24
### Changed
- The metapackage release notes are now properly extracting and formatting the module changes from the CHANGELOG.md file
- ## softcommerce/module-profile-schedule [1.3.7] - **Enhancement**: Included `servicepoint` type to account for `pakshop` facility.

## [1.3.7] - 2024-03-13
### Changed
- **Enhancement**: Included `servicepoint` type to account for `pakshop` facility.

## [1.3.6] - 2024-04-25
### Changed
- **Compatibility**: Introduced support for PHP 8.3

## [1.3.5] - 2024-02-02
### Fixed
- onetime schedule cache identifier [#3]

## [1.3.4] - 2024-02-01
### Added
- Introduce onetime schedule to run at specified times as part of cron job process. [#2]

## [1.3.3] - 2023-12-11
### Fixed
- No callbacks found for cron job plenty_customer_export. [#1]

## [1.3.2] - 2023-11-30
### Changed
- **Compatibility**: Add compatibility for Magento 2.4.6-p3 and Magento 2.4.7

## [1.3.1] - 2023-06-24
### Changed
- **Compatibility**: Add compatibility for PHP 8.2 and Magento 2.4.6-p1

## [1.3.0] - 2022-11-28
### Fixed
- Applied a fix to composer.json license compatibility.

## [1.2.9] - 2022-11-28
### Changed
- **Enhancement**: Moved mass status action for schedules from `SoftCommerce_Profile` to `SoftCommerce_ProfileSchedule`

## [1.2.8] - 2022-11-10
### Changed
- **Compatibility**: Compatibility with Magento [OS/AC] 2.4.5 and PHP 8.

## [1.2.7] - 2022-08-22
### Changed
- **Enhancement**: Added profile type ID filter to cron_schedule collection.

## [1.2.6] - 2022-08-16
### Changed
- **Enhancement**: Improvements to ACL rules.

## [1.2.5] - 2022-07-22
### Changed
- **Compatibility**: Compatibility with Magento Extension Quality Program (EQP).

## [1.2.4] - 2022-07-12
### Changed
- **Enhancement**: Allow inactive schedule process in order to collect profile data. Move `active/inactive` condition to each profile instead.

## [1.2.3] - 2022-07-03
### Changed
- **Enhancement**: Changes to PDT.

## [1.2.2] - 2022-06-11
### Changed
- **Improvement**: [M2P-4] Re-initialise config cache after saving new schedule cron task.

## [1.2.1] - 2022-06-09
### Changed
- **Compatibility**: JS Modal: IE9 break script loading and avoid execution on iframe [#5]

## [1.2.0] - 2022-06-08
### Changed
- **Compatibility**: Compatibility with Magento Open Source 2.4.4 [#4]

## [1.1.0] - 2022-06-09
### Changed
- **Compatibility**: Compatibility with Magento Open Source 2.4.3 [#2]
- **Enhancement**: Integration Tests [#1]

## [1.0.2] - 2022-06-10
### Changed
- **Compatibility**: JS backward compatibility for script rendering 2.3.5 - 2.4.2

## [1.0.1] - 2022-06-10
### Changed
- **Compatibility**: Compatibility with Magento Open Source 2.3.5 - 2.4.2 [#6]

## [1.0.0] - 2022-06-03
### Added
- [SCP-4] New schedule module used to handle profile schedules.

[Unreleased]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.3.7...HEAD
[1.3.7]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.3.6...v1.3.7
[1.3.6]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.3.5...v1.3.6
[1.3.5]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.3.4...v1.3.5
[1.3.4]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.3.3...v1.3.4
[1.3.3]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.3.2...v1.3.3
[1.3.2]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.3.1...v1.3.2
[1.3.1]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.2.9...v1.3.0
[1.2.9]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.2.8...v1.2.9
[1.2.8]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.2.7...v1.2.8
[1.2.7]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.2.6...v1.2.7
[1.2.6]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.2.5...v1.2.6
[1.2.5]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.2.4...v1.2.5
[1.2.4]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.2.3...v1.2.4
[1.2.3]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.2.2...v1.2.3
[1.2.2]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.2.1...v1.2.2
[1.2.1]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/softcommerceltd/magento-profile-schedule/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/softcommerceltd/magento-profile-schedule/releases/tag/v1.0.0
