<?php

namespace markhuot\CraftQL\Craft\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class RichText {

  function getDefinition($field) {
    return [
      $field->handle => [
        'type' => Type::string(),
        'args' => [
          ['name' => 'page', 'type' => Type::int()],
        ],
        'resolve' => function ($root, $args) use ($field) {
          if (!empty($args['page'])) {
            return $root->{$field->handle}->getPage($args['page']);
          }

          return $root->{$field->handle}->getParsedContent();
        }
      ],
    ];
  }

}
