<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class Query extends Component {

    private $sections;
    private $volumes;
    private $categoryGroups;
    private $assetVolumes;

    function __construct(
        \markhuot\CraftQL\Repositories\Volumes $volumes,
        \markhuot\CraftQL\Repositories\CategoryGroup $categoryGroups
    ) {
        $this->volumes = $volumes;
        $this->categoryGroups = $categoryGroups;
    }

    function getType() {
        $config = [
            'name' => 'Query',
            'fields' => [
                'helloWorld' => [
                    'type' => Type::string(),
                    'resolve' => function ($root, $args) {
                      return 'Welcome to GraphQL! You now have a fully functional GraphQL endpoint.';
                    }
                ],
                'entries' => [
                    'type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface()),
                    'description' => 'Entries from the craft interface',
                    'args' => \markhuot\CraftQL\Types\Section::args(),
                    'resolve' => function ($root, $args) {
                        $criteria = \craft\elements\Entry::find();
                        foreach ($args as $key => $value) {
                            $criteria = $criteria->{$key}($value);
                        }
                        return $criteria->all();
                    }
                ],
            ],
            'types' => $this->getTypes(),
        ];

        return new ObjectType($config);
    }

    function getTypes() {
        $this->volumes->loadAllVolumes();
        $this->categoryGroups->loadAllGroups();

        return array_merge(
            $this->volumes->getAllVolumes(),
            $this->categoryGroups->getAllGroups(),
            \markhuot\CraftQL\Types\EntryType::all()
        );
    }

}