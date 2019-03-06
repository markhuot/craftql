<?php

namespace markhuot\CraftQL\Services;

use Craft;
use Egulias\EmailValidator\Exception\CRLFAtTheEnd;
use GraphQL\GraphQL;
use GraphQL\Error\Debug;
use GraphQL\Type\Schema;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryComplexity;
use GraphQL\Validator\Rules\QueryDepth;
use markhuot\CraftQL\Builders\EnumObject;
use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\Directives\Date;
use markhuot\CraftQL\Events\AlterQuerySchema;
use markhuot\CraftQL\Helpers\StringHelper;
use markhuot\CraftQL\Types\Category;
use markhuot\CraftQL\Types\ElementInterface;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\Types\EntryConnection;
use markhuot\CraftQL\Types\EntryDraftConnection;
use markhuot\CraftQL\Types\EntryDraftEdge;
use markhuot\CraftQL\Types\EntryDraftInfo;
use markhuot\CraftQL\Types\EntryEdge;
use markhuot\CraftQL\Types\EntryInterface;
use markhuot\CraftQL\Types\EntryType;
use markhuot\CraftQL\Types\Field;
use markhuot\CraftQL\Types\Globals;
use markhuot\CraftQL\Types\PageInfo;
use markhuot\CraftQL\Types\Section;
use markhuot\CraftQL\Types\SectionSiteSettings;
use markhuot\CraftQL\Types\Site;
use markhuot\CraftQL\Types\Tag;
use markhuot\CraftQL\Types\Timestamp;
use markhuot\CraftQL\Types\User;
use markhuot\CraftQL\Types\Volume;
use yii\base\Component;
use Yii;

// - children(ancestorOf: Int, ancestorDist: Int, level: Int, descendantOf: Int, descendantDist: Int, fixedOrder: Boolean, group: [CategoryGroupsEnum], groupId: Int, id: [Int], indexBy: String, limit: Int, site: SitesEnum, siteId: Int, nextSiblingOf: Int, offset: Int, order: String, orderBy: String, positionedAfter: Int, positionedBefore: Int, prevSiblingOf: Int,                                  search: String, siblingOf: Int, slug: String, title: String, uri: String, testBlockCheckboxesField: TestBlockCheckboxesFieldEnum, testBlockPlainTextField: String, testBlockTableField: String, testCategoryField: String, testCategoryNestingField: String, testCheckboxes: TestCheckboxesEnum, testCheckboxesWithOneBadValue: TestCheckboxesWithOneBadValueEnum, testDateAndTimeField: String, testDateField: String, testDropdownField: TestDropdownFieldEnum, testEmailField: String, testLightswitchField: Boolean, testLightswitchOnField: Boolean, testMultiSelectField: TestMultiSelectFieldEnum, testNumberField: Int, testNumberFloatField: Float, testNumberMaxField: Int, testPlainText: String, testPlainTextWithCharacterLimit: String, testRadioButtonField: TestRadioButtonFieldEnum, testTableField: String, testTagField: Int, testTimeField: String, testUrlField: String): [CategoryInterface]
// + children(ancestorOf: Int, ancestorDist: Int, level: Int, descendantOf: Int, descendantDist: Int, fixedOrder: Boolean, group: [CategoryGroupsEnum], groupId: Int, id: [Int], indexBy: String, limit: Int, site: SitesEnum, siteId: Int, nextSiblingOf: Int, offset: Int, order: String, orderBy: String, positionedAfter: Int, positionedBefore: Int, prevSiblingOf: Int, relatedTo: [RelatedToInputType], search: String, siblingOf: Int, slug: String, title: String, uri: String, testBlockCheckboxesField: TestBlockCheckboxesFieldEnum, testBlockPlainTextField: String, testBlockTableField: String, testCategoryField: String, testCategoryNestingField: String, testCheckboxes: TestCheckboxesEnum, testCheckboxesWithOneBadValue: TestCheckboxesWithOneBadValueEnum, testDateAndTimeField: String, testDateField: String, testDropdownField: TestDropdownFieldEnum, testEmailField: String, testLightswitchField: Boolean, testLightswitchOnField: Boolean, testMultiSelectField: TestMultiSelectFieldEnum, testNumberField: Int, testNumberFloatField: Float, testNumberMaxField: Int, testPlainText: String, testPlainTextWithCharacterLimit: String, testRadioButtonField: TestRadioButtonFieldEnum, testTableField: String, testTagField: Int, testTimeField: String, testUrlField: String): [CategoryInterface]

class GraphQLService extends Component {

    private $schema;
    private $volumes;
    private $categoryGroups;
    private $tagGroups;
    private $entryTypes;
    private $sections;
    private $globals;
    private $sites;

    /**
     * Bootstrap the schema
     *
     * @return void
     */
    function bootstrap() {
        // @TODO don't load _everything_. Instead only load what's needed on demand
        \Yii::$container->get('craftQLFieldService')->load();

        $maxQueryDepth = CraftQL::getInstance()->getSettings()->maxQueryDepth;
        if ($maxQueryDepth !== false) {
            $rule = new QueryDepth($maxQueryDepth);
            DocumentValidator::addRule($rule);
        }

        $maxQueryComplexity = CraftQL::getInstance()->getSettings()->maxQueryComplexity;
        if ($maxQueryComplexity !== false) {
            $rule = new QueryComplexity($maxQueryComplexity);
            DocumentValidator::addRule($rule);
        }
    }

    function getSchema($token) {
        $request = new \markhuot\CraftQL\Request($token);

        $schemaConfig = [];

        $query = new \markhuot\CraftQL\Types\Query($request);

        $event = new AlterQuerySchema;
        $event->query = $query;
        $query->trigger(AlterQuerySchema::EVENT, $event);

        $request->registerNamespace('\\markhuot\\CraftQL\\Types');

        $request->registerType('Query', $query);

        array_map(function ($entryType) use ($request) {
            $request->registerType($entryType['craftQlTypeName'], function () use ($entryType, $request) {
                return new Entry($request, $entryType);
            });
        }, CraftQL::$plugin->entryTypes->all());

        array_map(function ($volume) use ($request) {
            $request->registerType($volume['craftQlTypeName'], function () use ($volume, $request) {
                return new Volume($request, $volume);
            });
        }, CraftQL::$plugin->volumes->all());

        array_map(function ($globalSet) use ($request) {
            $request->registerType(ucfirst($globalSet['handle']), function () use ($globalSet, $request) {
                return new Globals($request, $globalSet);
            });
        }, CraftQL::$plugin->globals->all());

        array_map(function ($categoryGroup) use ($request) {
            $request->registerType($categoryGroup['craftQlTypeName'], function() use ($categoryGroup, $request) {
                return new Category($request, $categoryGroup);
            });
        }, CraftQL::$plugin->categoryGroups->all());

        array_map(function ($tagGroup) use ($request) {
            $request->registerType($tagGroup['craftQlTypeName'], function () use ($tagGroup, $request) {
                return new Tag($request, $tagGroup);
            });
        }, CraftQL::$plugin->tagGroups->all());

        // This is a callback because we're going to defer the loading of all types until
        // the underlying graphql-php determines we actually _need_ all of the types
        $schemaConfig['types'] = function () use ($request) {
            return array_merge($request->getAllTypes(), [
                $request->getType('DateFormatTypes'),
                $request->getType('CategoryGroupsEnum'),
            ]);
        };

        $schemaConfig['query'] = $request->getType('Query');

        $schemaConfig['typeLoader'] = function ($name) use ($request) {
            if ($request->getType($name)) {
                return $request->getType($name);
            }

            throw new \Exception($name.' could not be found');
        };

        $schemaConfig['directives'] = array_merge(GraphQL::getStandardDirectives(), [
            (new Date($request))->getRawGraphQLObject()
        ]);

        $mutation = new \markhuot\CraftQL\Types\Mutation($request);
        $request->registerType('Mutation', $mutation);
        $schemaConfig['mutation'] = $mutation->getRawGraphQLObject();

        $schema = new Schema($schemaConfig);

        if (Craft::$app->config->general->devMode && CraftQL::getInstance()->getSettings()->disableSchemaValidation === false) {
            $schema->assertValid();
        }

        return $schema;
    }

    function execute($schema, $input, $variables = []) {
        $debug = Craft::$app->config->getGeneral()->devMode ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::RETHROW_INTERNAL_EXCEPTIONS : null;
        return GraphQL::executeQuery($schema, $input, null, null, $variables)->toArray($debug);
    }

}
