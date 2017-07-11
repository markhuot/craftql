<?php

namespace markhuot\CraftQL\Services;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\GraphQL;
use GraphQL\Schema;
use Underscore\Types\Arrays;
use markhuot\CraftQL\Plugin;
use yii\base\Component;

class GraphQLService extends Component {

    private $schema;
    private $timers = [];

    private $sections;
    private $tagGroups;
    private $categoryGroups;
    private $assetVolumes;

    function __construct(
        \markhuot\CraftQL\Services\SchemaSectionService $sections,
        \markhuot\CraftQL\Services\SchemaTagGroupService $tagGroups,
        \markhuot\CraftQL\Services\SchemaCategoryGroupService $categoryGroups,
        \markhuot\CraftQL\Services\SchemaAssetVolumeService $assetVolumes
    ) {
        $this->sections = $sections;
        $this->tagGroups = $tagGroups;
        $this->categoryGroups = $categoryGroups;
        $this->assetVolumes = $assetVolumes;
    }

    function bootstrap() {
        $this->timers['start'] = microtime(true) * 1000;

        // Eager load some things we know we'll need later
        $this->tagGroups->loadAllGroups();
        $this->categoryGroups->loadAllGroups();
        $this->sections->loadAllSections();
        $this->assetVolumes->loadAllVolumes();

        $queryTypeConfig = [
            'name' => 'Query',
            'fields' => [
                'me' => [
                    'type' => Type::string(),
                    'resolve' => function ($root, $args) {
                      return 'wooot!';
                    }
                ]
            ],
            'types' => [],
        ];

        foreach ($this->sections->loadedSections() as $handle => $sectionType) {
            $isSingle = $sectionType->config['type'] == 'single';

            $queryTypeConfig['fields'][$handle] = [
                'type' => $isSingle ? $sectionType : Type::listOf($sectionType),
                'description' => 'Entries from the '.$handle.' section',
                'args' => [
                    'after' => Type::string(),
                    'ancestorOf' => Type::int(),
                    'ancestorDist' => Type::int(),
                    'archived' => Type::boolean(),
                    'authorGroup' => Type::string(),
                    'authorGroupId' => Type::int(),
                    'authorId' => Type::int(),
                    'before' => Type::string(),
                    'level' => Type::int(),
                    'localeEnabled' => Type::boolean(),
                    'descendantOf' => Type::int(),
                    'descendantDist' => Type::int(),
                    'fixedOrder' => Type::boolean(),
                    'id' => Type::int(),
                    'limit' => Type::int(),
                    'locale' => Type::string(),
                    'nextSiblingOf' => Type::int(),
                    'offset' => Type::int(),
                    'order' => Type::string(),
                    'positionedAfter' => Type::id(),
                    'positionedBefore' => Type::id(),
                    'postDate' => Type::string(),
                    'prevSiblingOf' => Type::id(),
                    'relatedTo' => Type::id(),
                    'search' => Type::string(),
                    'siblingOf' => Type::int(),
                    'slug' => Type::string(),
                    'status' => Type::string(),
                    'title' => Type::string(),
                    'type' => Type::string(),
                    'uri' => Type::string(),
                ],
                'resolve' => function ($root, $args) use ($handle, $isSingle) {
                    $criteria = \craft\elements\Entry::find();
                    $criteria = $criteria->section($handle);
                    foreach ($args as $key => $value) {
                        $criteria = $criteria->{$key}($value);
                    }
                    return $isSingle ? $criteria->one() : $criteria->find();
                }
            ];
        }

        foreach ($this->categoryGroups->loadedGroups() as $handle => $group) {
            $queryTypeConfig['fields'][$handle] = [
                'type' => Type::listOf($group),
                'resolve' => function ($root, $args) use ($handle) {
                    $criteria = \craft\elements\Entry::find();
                    $criteria = $criteria->group($handle);
                    return $criteria->find();
                },
            ];
        }

        // $queryTypeConfig['fields']['uris'] = [
        //     'type' => Type::listOf(Plugin::$schemaElementService->getInterface()),
        //     'resolve' => function ($root, $args) {
        //         $elements = [];

        //         // Stolen from ElementsService::getElementByUri
        //         $result = Craft::$app->db->createCommand()
        //             ->select('elements.id as element_id, elements.type as element_type, elements_i18n.locale as element_local, elements_i18n.uri as element_uri')
        //             ->from('elements elements')
        //             ->join('elements_i18n elements_i18n', 'elements_i18n.elementId = elements.id')
        //             ->andWhere('elements_i18n.uri IS NOT NULL')
        //             ->andWhere(['IN', 'elements.type', ['Entry', 'Category']])
        //             ->queryAll();

        //         $result = Arrays::group($result, function ($row) {
        //             return $row['element_type'];
        //         });

        //         foreach ($result as $elementType => $elementRows) {
        //             $elementIds = Arrays::pluck($elementRows, 'element_id');

        //             $criteria = \craft\elements\Entry::find();
        //             $criteria = $criteria->entryId($elementIds);

        //             $elements = array_merge($elements, $criteria->find());
        //         }

        //         return $elements;
        //     }
        // ];

        $queryType = new ObjectType($queryTypeConfig);

        $this->schema = new Schema([
            'query' => $queryType,
            'types' => array_merge([],
                $this->assetVolumes->getAllVolumes()
            ),
        ]);

        $this->timers['setup'] = microtime(true) * 1000;
        // $this->timers['total1'] = $this->timers['setup']-$this->timers['start'];
    }

    function execute($input, $variables = []) {
        $result = GraphQL::execute($this->schema, $input, null, null, $variables);

        $this->timers['end'] = microtime(true) * 1000;
        // $this->timers['total2'] = $this->timers['end']-$this->timers['start'];

        return $result;
    }

    function getTimers() {
        return $this->timers;
    }

}
