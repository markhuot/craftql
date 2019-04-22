<?php

namespace markhuot\CraftQL\Services;

use Craft;
use markhuot\CraftQL\Helpers\StringHelper;
use markhuot\CraftQL\Models\Token;

class EntryTypesService {

    private $entryTypes = [];

    function __construct() {
        $entryTypes = (new \craft\db\Query())
            ->select([
                'id',
                'sectionId',
                'fieldLayoutId',
                'name',
                'handle',
                'hasTitleField',
                'titleLabel',
                'titleFormat',
            ])
            ->from(['{{%entrytypes}}'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($entryTypes as $entryType) {
            $craftQlTypeName = StringHelper::graphQLNameForEntryType($entryType);
            $entryType['craftQlTypeName'] = $craftQlTypeName;
            $this->entryTypes[$entryType['id']] = $entryType;
        }
    }

    function getById($id) {
        return $this->entryTypes[$id];
    }

    function all() {
        return $this->entryTypes;
    }

}