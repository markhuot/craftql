<?php

namespace markhuot\CraftQL\services;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use yii;
use yii\base\Component;
use markhuot\CraftQL\Services\SchemaSectionService;

class SchemaEntryService extends Component {

  static $interface;
  static $baseFields;
  private $sections;

  // function __construct(
  //     \markhuot\CraftQL\Services\SchemaSectionService $sections
  // ) {
  //     $this->sections = $sections;
  // }

  function baseFields() {
    if (!empty(static::$baseFields)) {
      return static::$baseFields;
    }

    $sectionType = new ObjectType([
      'name' => 'Section',
      'fields' => [
        'id' => ['type' => Type::nonNull(Type::int())],
        'structureId' => ['type' => Type::nonNull(Type::int())],
        'name' => ['type' => Type::nonNull(Type::string())],
        'handle' => ['type' => Type::nonNull(Type::string())],
        'type' => ['type' => Type::nonNull(Type::string())],
        'template' => ['type' => Type::nonNull(Type::string())],
        'maxLevels' => ['type' => Type::nonNull(Type::int())],
        'hasUrls' => ['type' => Type::nonNull(Type::boolean())],
        'enableVersioning' => ['type' => Type::nonNull(Type::boolean())],
      ],
    ]);

    $entryType = new ObjectType([
      'name' => 'EntryType',
      'fields' => [
        'id' => ['type' => Type::nonNull(Type::int())],
        'name' => ['type' => Type::nonNull(Type::string())],
        'handle' => ['type' => Type::nonNull(Type::string())],
      ],
    ]);

    $fields = [];
    $fields['elementType'] = ['type' => Type::nonNull(Type::string()), 'resolve' => function ($root, $args) {
      return 'Entry';
    }];
    $fields['id'] = ['type' => Type::nonNull(Type::int())];
    $fields['title'] = ['type' => Type::nonNull(Type::string())];
    $fields['slug'] = ['type' => Type::nonNull(Type::string())];
    $fields['dateCreatedTimestamp'] = ['type' => Type::nonNull(Type::int()), 'resolve' => function ($root, $args) {
      return $root->dateCreated->format('U');
    }];
    $fields['dateCreated'] = ['type' => Type::nonNull(Type::string()), 'args' => [
      ['name' => 'format', 'type' => Type::string(), 'defaultValue' => 'r']
    ], 'resolve' => function ($root, $args) {
      return $root->dateCreated->format($args['format']);
    }];
    $fields['dateUpdatedTimestamp'] = ['type' => Type::nonNull(Type::int()), 'resolve' => function ($root, $args) {
      return $root->dateUpdated->format('U');
    }];
    $fields['dateUpdated'] = ['type' => Type::nonNull(Type::int()), 'args' => [
      ['name' => 'format', 'type' => Type::string(), 'defaultValue' => 'r']
    ], 'resolve' => function ($root, $args) {
      return $root->dateUpdated->format($args['format']);
    }];
    $fields['expiryDateTimestamp'] = ['type' => Type::int(), 'resolve' => function ($root, $args) {
      return $root->expiryDate->format('U');
    }];
    $fields['expiryDate'] = ['type' => Type::nonNull(Type::int()), 'args' => [
      ['name' => 'format', 'type' => Type::string(), 'defaultValue' => 'r']
    ], 'resolve' => function ($root, $args) {
      return $root->expiryDate->format($args['format']);
    }];
    $fields['enabled'] = ['type' => Type::nonNull(Type::boolean())];
    $fields['status'] = ['type' => Type::nonNull(Type::string())];
    $fields['uri'] = ['type' => Type::string()];
    $fields['url'] = ['type' => Type::string()];
    $fields['section'] = ['type' => $sectionType, 'resolve' => function ($root, $args) {
      return $root->section;
    }];
    $fields['type'] = ['type' => $entryType, 'resolve' => function ($root, $args) {
      return $root->type;
    }];

    return static::$baseFields = $fields;
  }

  function getInterface() {
    if (!static::$interface) {
      $fields = $this->baseFields();

      static::$interface = new InterfaceType([
        'name' => 'EntryInterface',
        'description' => 'An entry in Craft',
        'fields' => $fields,
        'resolveType' => function ($entry) {
          return ucfirst($entry->section->handle);
        }
      ]);
    }

    return static::$interface;
  }

  function getGraphQLField() {
    $sections = Yii::$container->get(SchemaSectionService::class);

    return [
      'type' => Type::listOf($this->getInterface()),
      'description' => 'Entries from the craft interface',
      'args' => $sections->getSectionArgs(),
      'resolve' => function ($root, $args) {
        $criteria = \craft\elements\Entry::find();
        foreach ($args as $key => $value) {
          $criteria = $criteria->{$key}($value);
        }
        return $criteria->find();
      }
    ];
  }

  /**
   * A Relay-compatable field that includes pagination
   *
   * @return array
   */
  function getGraphQLFieldPaginator() {
    $sections = Yii::$container->get(SchemaSectionService::class);

    $edges = new ObjectType([
      'name' => 'Edges',
      'fields' => [
        'node' => ['type' => $this->getInterface(), 'resolve' => function ($root, $args) {
          return $root;
        }],
        'cursor' => ['type' => Type::nonNull(Type::string()), 'resolve' => function ($root, $args) {
          return base64_encode($root->id);
        }],
      ],
    ]);

    $pageInfo = new ObjectType([
      'name' => 'PageInfo',
      'fields' => [
        'hasNextPage' => ['type' => Type::nonNull(Type::boolean())],
        'hasPreviousPage' => ['type' => Type::nonNull(Type::boolean())],
      ],
    ]);

    $paginator = new ObjectType([
      'name' => 'Paginator',
      'fields' => [
        'pageInfo' => ['type' => $pageInfo, 'resolve' => function ($root, $args) {
          $total = $root->total();
          $offset = $root->offset;
          $perPage = $root->limit;
          return [
            'hasNextPage' => $perPage !== null && $offset + $perPage < $total,
            'hasPreviousPage' => $offset > 0,
          ];
        }],
        'edges' => ['type' => Type::listOf($edges), 'resolve' => function ($root, $args) {
          return $root->find();
        }],
      ],
    ]);

    $args = $sections->getSectionArgs();
    $args = array_merge($args, [
      'first' => Type::int(),
      'after' => Type::string(),
    ]);

    return [
      'type' => $paginator,
      'args' => $args,
      'resolve' => function ($root, $args) {
        $criteria = \craft\elements\Entry::find();
        foreach ($args as $key => $value) {
          switch ($key) {
            case 'first':
              $criteria = $criteria->limit($value);
              break;
            case 'after':
              $criteria = $criteria->where(['>', 'entries.id', base64_decode($value)]);
              break;
            default:
              $criteria = $criteria->{$key}($value);
          }
        }
        return $criteria;
      }
    ];
  }

  function getGraphQLFields() {
    return [
      'entries' => $this->getGraphQLField(),
      'entriesPaginator' => $this->getGraphQLFieldPaginator(),
    ];
  }

}
