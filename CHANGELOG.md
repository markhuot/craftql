## dev-master

- (none)

# 1.1.0 - 2018-06-27

- upsert mutations now set the `authorId` based on the token's user, where applicable.
- #83 allows the top level `categories` field to accept an array of `id:` integers now.
- downloads GraphiQL into a Craft Asset Bundle so that you can browse without an internet connection
- #67 adds a new CLI command `./craft craftql/tools/fetch-fragment-types > fragments.json` to help with Apollo fragment matching
- #90 removes `nonNull` uriFormat for sections that `hasUrls` is `false`
- #79 adds support for the Videos 2 plugin
- #67 adds `type:` and `limit:` arguments to matrix fields for server-side filtering
- #95 adds a new CLI command `./craft craftql/tools/print-schema > schema.graphql` for related tooling such as Apollo's `generate` command
- updates some Craft API interactions to avoid deprecation notices
- #94 adds `transform:` argument to an image field's `width` and `height` fields so you can get back the transformed size
- #98 adds a `relatedCategories` field to the `EntryEdge` to pull back all categories that may not be directly linked through a field
- #101 adds a `query:` argument to mutations so you have more control over what is being upserted
- #101 adds an `enabled:` argument to set the enabled state of the entry
- #105 adds an `id:` argument to matrix upserts so you can overwrite an existing matrix blocks instead of always creating a new block
- #107 adds a `siteId:` argument to entry mutations to both query and set the `siteId` of the upserted entry
- #97 fixed some error messages when you try to pull an entry out of a related entries field that you don't have access to through your token
- #109 adds support for the native Radio Button field type
- #108 fixes a invalid schema bug when you enable categories but don't have any category groups
- #104 adds a top level `assets` field that can query assets just as `craft.assets` does. This has a configurable permission, as well
- #14 adds custom field support when querying assets
- #110 adds depth and complexity validators for basic security
- #111 fixes some bad documentation

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