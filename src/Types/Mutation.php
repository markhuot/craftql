<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class Mutation extends Component {

    private $sections;

    function __construct(
        \markhuot\CraftQL\Repositories\Sections $sections
    ) {
        $this->sections = $sections;
    }

    function getType() {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        $this->sections->loadAllSections();

        $entryTypes = \markhuot\CraftQL\Types\EntryType::all();
        foreach ($entryTypes as $entryType) {
            $args = \markhuot\CraftQL\Types\Entry::baseArgs();
            $args = array_merge($args, $entryType->args());

            $fields['upsert'.ucfirst($entryType->name())] = [
                'type' => $entryType,
                'args' => $args,
                'resolve' => $entryType->upsert(),
            ];
        }

        return new ObjectType([
            'name' => 'Mutation',
            'fields' => $fields
        ]);
    }

}