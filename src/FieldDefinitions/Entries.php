<?php

namespace markhuot\CraftQL\FieldDefinitions;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Entries extends Component {

  private $entries;

  function __construct(
    \markhuot\CraftQL\Services\SchemaEntryService $entries
  ) {
    $this->entries = $entries;
  }

  function getDefinition($field) {
    return [$field->handle => [
      'type' => Type::listOf($this->entries->getInterface()),
    ]];
  }

}
