<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CraftQL_SchemaSectionService extends BaseApplicationComponent {

  private $sections = [];

  function loadAllSections() {
    foreach (craft()->sections->allSections as $section) {
      $this->sections[$section->handle] = $this->parseSectionToObject($section);
    }
  }

  function getSection($sectionHandle) {
    if (!isset($this->sections[$sectionHandle])) {
      $section = craft()->sections->getSectionByHandle($sectionHandle);
      $this->sections[$sectionHandle] = $this->parseSectionToObject($section);
    }

    return $this->sections[$sectionHandle];
  }

  function loadedSections() {
    return $this->sections;
  }

  function parseSectionToObject($section) {
    $fields = craft()->craftQL_fieldEntries->baseFields();

    foreach ($section->entryTypes as $entryType) {
      $fields = array_merge($fields, craft()->craftQL_field->getFields($entryType->fieldLayoutId));
    }

    return new ObjectType([
      'name' => ucfirst($section->handle),
      'fields' => $fields,
      'interfaces' => [
        craft()->craftQL_fieldEntries->getInterface()
      ],
      'isSingle' => $section->type == 'single',
    ]);
  }

}
