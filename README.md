A drop-in GraphQL server for your [Craft CMS](https://craftcms.com/) implementation. With zero configuration, _CraftQL_ allows you to access all of Craft's features through a familiar [GraphQL](http://graphql.org) interface.

## Example

Once installed, you can query Craft CMS using almost the exact same syntax as your Twig templates.

```gql
{
  news(limit: 10) {
    title
    url
    body
  }
}
```

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
