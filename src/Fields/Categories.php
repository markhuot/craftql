<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Categories extends Component {

    private $categoryGroups;

    function __construct(
        \markhuot\CraftQL\Repositories\CategoryGroup $categoryGroups
    ) {
        $this->categoryGroups = $categoryGroups;
    }

  function getDefinition($field) {
    preg_match('/^group:(\d+)$/', $field->source, $matches);
    $groupId = $matches[1];

    // var_dump('2');
    // die;
    return [$field->handle => [
      'type' => Type::listOf($this->categoryGroups->getGroup($groupId)),
      'description' => $field->instructions,
      'resolve' => function ($root, $args) use ($field) {
        return $root->{$field->handle}->all();
      }
    ]];
  }

}
