<?php

namespace markhuot\CraftQL\Types;

/**
 * Class RelatedToInputType
 * @package markhuot\CraftQL\Types
 * @craftql-type input
 */
class RelatedToInputType {

    /**
     * @var int[]
     */
    public $element;

    /**
     * @var boolean
     */
    public $elementIsEdge;

    /**
     * @var int[]
     */
    public $sourceElement;

    /**
     * @var boolean
     */
    public $sourceElementIsEdge;

    /**
     * @var int[]
     */
    public $targetElement;

    /**
     * @var boolean
     */
    public $targetElementIsEdge;

    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $sourceLocale;

}