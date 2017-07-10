<?php

namespace markhuot\CraftQL\services;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use yii\base\Component;

class SchemaSectionService extends Component {

  private $sections = [];
  private $entries;
  private $elements;
  private $fields;

  function __construct(
    \markhuot\CraftQL\Services\SchemaEntryService $entries,
    \markhuot\CraftQL\Services\SchemaElementService $elements,
    \markhuot\CraftQL\Services\FieldService $fields
  ) {
    $this->entries = $entries;
    $this->elements = $elements;
    $this->fields = $fields;
  }

  function loadAllSections() {
    foreach (Craft::$app->sections->allSections as $section) {
      $this->sections[$section->handle] = $this->parseSectionToObject($section);
    }
  }

  function getSection($sectionHandle) {
    if (!isset($this->sections[$sectionHandle])) {
      $section = Craft::$app->sections->getSectionByHandle($sectionHandle);
      $this->sections[$sectionHandle] = $this->parseSectionToObject($section);
    }

    return $this->sections[$sectionHandle];
  }

  function loadedSections() {
    return $this->sections;
  }

  function parseSectionToObject($section) {
    $fields = $this->entries->baseFields();

    foreach ($section->entryTypes as $entryType) {
      $fields = array_merge($fields, $this->fields->getFields($entryType->fieldLayoutId));
    }

    return new ObjectType([
      'name' => ucfirst($section->handle),
      'fields' => $fields,
      'interfaces' => [
        $this->entries->getInterface(),
        $this->elements->getInterface(),
      ],
      'type' => $section->type,
    ]);
  }

}
