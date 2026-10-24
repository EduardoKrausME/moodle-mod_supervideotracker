# Changelog

This changelog documents only the two most recent published GitHub releases. Each entry describes verified changes between its release tag and the immediately preceding published release. Later changes on `master` are excluded.

## 1.0.4 - 2026-10-15

### Changed

- Updated only the plugin release metadata in `version.php`: release identifier from `1.0.3` to `1.0.4` and build number from `2026101500` to `2026101501`.

No changes to Super Video Tracker activity functionality or other files were identified in this tag comparison.

[Compare 1.0.3...1.0.4](https://github.com/EduardoKrausME/moodle-mod_supervideotracker/compare/1.0.3...1.0.4)

## 1.0.3 - 2026-10-15

### Changed

- Updated the GitHub Actions CI PostgreSQL service from version 16 to 17.
- Updated the MariaDB CI service from version 10 to 11.4, replacing the previous startup environment variables and changing the health-check command to `healthcheck.sh --connect --innodb_initialized`.
- Updated the plugin release identifier from `1.0.2` to `1.0.3` and its build number from `2026101200` to `2026101500`, with a reordering of metadata lines in `version.php`.

No functional changes to the Super Video Tracker activity were identified in this tag comparison.

[Compare 1.0.2...1.0.3](https://github.com/EduardoKrausME/moodle-mod_supervideotracker/compare/1.0.2...1.0.3)
