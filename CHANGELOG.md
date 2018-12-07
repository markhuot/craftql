## dev-master

- (none)

## 1.2.0 - 2018-12-07

### Fixed
- Released a bunch of features! ([#192](https://github.com/markhuot/craftql/issues/192))
- Added `limit:` argument to asset fields on `EntryInterface` ([#191](https://github.com/markhuot/craftql/issues/191))
- Added a scope for global mutations ([#185](https://github.com/markhuot/craftql/issues/185))
- Added the ability to set `postDate` and `expiryDate` on `upsertEntryType` fields ([#181](https://github.com/markhuot/craftql/issues/181))
- Fixed a bug where matrix fields were not saving all data because the field's `onSave` was not called ([#180](https://github.com/markhuot/craftql/issues/180))
- Fixed a bug where a field in Craft points to a category group that no longer exists, it now silently skips the field ([#178](https://github.com/markhuot/craftql/issues/178))
- Added a `*Connection` field to all category fields so you can get more detail from categories fields (yo dawg) ([#177](https://github.com/markhuot/craftql/issues/177))
- Added a top level `upsertUser` which allows setting user fields when passed a user id or creates a new user when null ([#172](https://github.com/markhuot/craftql/issues/172))
- Added an error message whenever CraftQL creates a bad GraphQL enum value through inference ([#156](https://github.com/markhuot/craftql/issues/156))
- Added a `*_FieldData` field for all Select field types to allow pulling the possible options as well as the selected option ([#155](https://github.com/markhuot/craftql/issues/155))
- Fixed a bug where Redactor fields were creating duplicate GraphQL Object Types with the same schema ([#153](https://github.com/markhuot/craftql/issues/153))
- Added a `group:` argument to the top level `categories` field to filter the returned categories ([#152](https://github.com/markhuot/craftql/issues/152))
- Added `parent`, `next`, `nextSibling`, `prev`, and `prevSibling` to `CategoryInterface` ([#149](https://github.com/markhuot/craftql/issues/149))
- Fixed a bug where you couldn't perform a mutation without also having query scope access ([#146](https://github.com/markhuot/craftql/issues/146))
- Changed the `relatedTo:` argument to support a list of element ids. This is backwards compatible because GraphQL allows single values to be treated as a list ([#144](https://github.com/markhuot/craftql/issues/144))
- Increased the token field in the DB to `text` to support larger `scope` arrays ([#139](https://github.com/markhuot/craftql/issues/139))
- Added support for [Geo Address](https://github.com/tdeNL/craftplugin-geoaddress) field types ([#138](https://github.com/markhuot/craftql/issues/138))
- Added an `AlterQuerySchema` event to allow 3rd-parties to add root fields to the schema ([#134](https://github.com/markhuot/craftql/issues/134))
- Fixed an upsert bug when trying to set an enum to `empty` because the Craft field handle is an empty string ([#129](https://github.com/markhuot/craftql/issues/129))
- Added a top level `sites` field and associated query scope ([#123](https://github.com/markhuot/craftql/issues/123))
- Added a `focalPoint` field to `VolumeInterface` ([#120](https://github.com/markhuot/craftql/issues/120))
- Added the ability to query category children from a `categories` or `categoriesConnection` field ([#119](https://github.com/markhuot/craftql/issues/119))
- Fixed a bug where new creating categories were failing during a mutation ([#116](https://github.com/markhuot/craftql/issues/116))
- Fixed a bug where the `pageInfo` field was not calculated correctly on connections ([#115](https://github.com/markhuot/craftql/issues/115))

## 1.1.0 - 2018-06-27

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