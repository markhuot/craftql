![CraftQL seen through the GraphiQL UI](https://raw.githubusercontent.com/markhuot/craftql/master/assets/graphiql.png)

A drop-in [GraphQL](http://graphql.org) server for your [Craft CMS](https://craftcms.com/) implementation. With zero configuration, _CraftQL_ allows you to access all of Craft's features through a familiar GraphQL interface.

<hr>

**Note:**, this plugin may or may not become a paid add-on when the Craft Plugin store becomes available. <strike>Buyer</strike> Downloader beware.

<hr>

## Examples

Once installed, you can test your installation with a simple Hello World,

```graphql
{
  helloWorld
}
```

If that worked, you can now query Craft CMS using almost the exact same syntax as your Twig templates.

```graphql
{
  entries(section:[news], limit:5, search:"body:salty") {
    ...on News {
      title
      url
      body
    }
  }
}
```

_CraftQL_ provides a top level `entries` field that takes the same arguments as `craft.entries` does in your template. This is the most commonly used field/access point. E.g.,

```graphql
query fetchNews {             # The query, `query fetchNews` is completely optional
  entries(section:[news]) {   # Arguments match `craft.entries`
    ...on News {              # GraphQL is strongly typed, so you must specify each Entry Type you want data from
      id                      # A field to return
      title                   # A field to return
      body                    # A field to return
    }
  }
}
```

Types are automatically created for every Entry Type in your install. If you have a section named `news` and an entry type named `news` the GraphQL type will be named `News`. If you have a section named `news` and an entry type named `pressRelease` the GraphQL type will be named `NewsPressRelease`. The convention is to mash the section handle and the entry type handle together, unless they are the same, in which case the section handle will be used.

```graphql
query fetchNews {
  entries(section:[news]) {
    ...on News {              # Any fields on the News entry type
      id
      title
      body
    }
    ...on NewsPressRelease {  # Any fields on the Press Release entry type
      id
      title
      body
      source
      contactInfo
      downloads {
        title
        url
      }
    }
  }
}
```

To modify content make sure your token has write access and then use the top level `upsert{EntryType}` `Mutation`. `upsert{EntryType}` takes arguments for each field defined in Craft. 

```graphql
mutation createNewEntry($title:String, $body:String) {
  upsertNews(
    title:$title,
    body:$body,
  ) {
    id
    url
  }
}
```

The above would be passed with variables such as,

```json
{
  "title": "My first mutation!",
  "body": "<p>Here's the body of my first mutation</p>",
}
```

## Security

CraftQL supports GraphQl field level permissions. By default a token will have no rights. You must click into the "Scopes" section to adjust what each token can do.

![token scopes](https://raw.githubusercontent.com/markhuot/craftql/master/assets/scopes.png)

Scopes allow you to configure which GraphQL fields and entry types are included in the schema.

## Roadmap

No software is ever done. There's a lot still to do in order to make _CraftQL_ feature complete. Some of the outstanding items include,

- [x] Matrix fields are not included in the schema yet
- [x] Table fields are not included in the schema yet
- [x] Asset mutations (implemented by passing a URL or asset id)
- [ ] File uploads to assets fields during a mutation
- [ ] Automated testing is not functional yet
- [x] Mutations need a lot more testing

## Requirements

- Craft 3.0
- PHP 7.0+

## Installation

If you don't have Craft 3 installed yet, do that first:

```shell
$ composer create-project craftcms/craft my-awesome-site -s beta
```

Once you have a running version of Craft 3 you can install _CraftQL_ with Composer:

```shell
$ composer require markhuot/craftql
```

## Running the CLI server

_CraftQL_ ships with a PHP-native web server. When running _CraftQL_ through the provided web server the bootstrapping process will only happen during the initial start up. This has the potential to greatly speed up responses times since PHP will persist state between requests. In general, I have seen performance improvements of 5x (500ms to <100ms).

Caution: this can also create unintended side effects since Craft is not natively built to run this way. Do not use this in production it could lead to memory leaks, server fires, and IT pager notifications :).

```
php craft craftql/server
```
