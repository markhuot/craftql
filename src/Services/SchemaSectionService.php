<?php

namespace markhuot\CraftQL\services;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;

class SchemaSectionService {

  private $sections = [];

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
    $fields = Plugin::$schemaEntryService->baseFields();

    foreach ($section->entryTypes as $entryType) {
      $fields = array_merge($fields, Plugin::$fieldService->getFields($entryType->fieldLayoutId));
    }

    return new ObjectType([
      'name' => ucfirst($section->handle),
      'fields' => $fields,
      'interfaces' => [
        Plugin::$schemaEntryService->getInterface(),
        Plugin::$schemaElementService->getInterface(),
      ],
      'type' => $section->type,
    ]);
  }

}
