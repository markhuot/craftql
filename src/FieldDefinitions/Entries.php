<?php

namespace markhuot\CraftQL\FieldDefinitions;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Entries extends Component {

  private $entries;
  private $sections;

  function __construct(
    \markhuot\CraftQL\Services\SchemaEntryService $entries,
    \markhuot\CraftQL\Services\SchemaSectionService $sections
  ) {
    $this->entries = $entries;
    $this->sections = $sections;
  }

  function getDefinition($field) {
    return [$field->handle => [
      'type' => Type::listOf($this->entries->getInterface()),
      'args' => $this->sections->getSectionArgs(),
      'resolve' => function ($root, $args) use ($field) {
        $criteria = $root->{$field->handle};
        foreach ($args as $key => $value) {
          $criteria->{$key} = $value;
        }
        return $criteria;
      }
    ]];
  }

}
