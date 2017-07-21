<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Tags extends Component {

  private $tagGroups;

  function __construct(
    \markhuot\CraftQL\Repositories\TagGroups $tagGroups
  ) {
    $this->tagGroups = $tagGroups;
  }

  function getDefinition($field) {
    $source = $field->settings['source'];
    if (preg_match('/taggroup:(\d+)/', $source, $matches)) {
      $groupId = $matches[1];
      return [$field->handle => [
        'type' => Type::listOf($this->tagGroups->getGroup($groupId)),
        'description' => $field->instructions,
      ]];
    }
  }

  function getGraphQlType($field) {
    return Type::string();
  }

}
