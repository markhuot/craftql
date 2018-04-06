## dev-master

- Upsert mutations now set the `authorId` based on the token's user, where applicable.
- Allows the top level `categories` field to accept an array of `id:` integers now.

## 1.0.0 - 2018-04-04

- Initial public release!

## 1.0.0-beta.8 - 2017-08-24

### Fixed

- Date fields now use the Timestamp scalar type

## 1.0.0-beta.7 - 2017-08-23

### Added

- A `@date` directive is now used to format `Timestamp` scalars. Use it with the same options Carbon uses, `@date(as:"Y-m-d")`

### Changed

- `dateCreated`, `dateUpdated`, and `expiryDate` now use a new `Timestamp` scalar

## 1.0.0 - 2017-07-11

### New

- Initial release