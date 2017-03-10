<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CraftQL_SchemaSectionService extends BaseApplicationComponent {

  public $sections = [];

  function loadAllSections() {
    foreach (craft()->sections->allSections as $section) {
      $this->sections[$section->handle] = $this->parseSectionToObject($section);
    }
  }

  function getSection($sectionHandle) {
    if (!isset($this->sections[$sectionHandle])) {
      $section = craft()->sections->getSectionById($sectionHandle);
      $this->sections[$sectionHandle] = $this->parseSectionToObject($section);
    }

    return $this->sections[$sectionHandle];
  }

  function parseSectionToObject($section) {
    $sectionTypeFields = [];
    $sectionTypeFields['id'] = ['type' => Type::int()];
    $sectionTypeFields['title'] = ['type' => Type::string()];
    $sectionTypeFields['slug'] = ['type' => Type::string()];
    $sectionTypeFields['dateCreated'] = ['type' => Type::int(), 'resolve' => function ($root, $args) {
      return $root->dateCreated->format('U');
    }];
    $sectionTypeFields['dateUpdated'] = ['type' => Type::int(), 'resolve' => function ($root, $args) {
      return $root->dateUpdated->format('U');
    }];
    $sectionTypeFields['expiryDate'] = ['type' => Type::int(), 'resolve' => function ($root, $args) {
      return $root->expiryDate->format('U');
    }];
    $sectionTypeFields['enabled'] = ['type' => Type::boolean()];
    $sectionTypeFields['status'] = ['type' => Type::string()];
    $sectionTypeFields['uri'] = ['type' => Type::string()];
    $sectionTypeFields['url'] = ['type' => Type::string()];

    foreach ($section->entryTypes as $entryType) {
      $sectionTypeFields = array_merge($sectionTypeFields, craft()->craftQL_schemaField->getFields($entryType->fieldLayoutId));
    }

    return new ObjectType([
      'name' => $section->name,
      'fields' => $sectionTypeFields,
    ]);
  }

}
