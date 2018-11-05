<?php

namespace markhuot\CraftQL\Types;

/**
 * Class VolumeInterface
 * @package markhuot\CraftQL\Types
 * @craftql-type interface
 */
class VolumeInterface {

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $width;

    /**
     * @var string
     */
    public $height;

    /**
     * @var int
     */
    public $size;

    /**
     * @var VolumeFolder
     */
    public $folder;

    /**
     * @var string
     */
    public $volumeId;

    /**
     * @var string
     */
    public $mimeType;

    /**
     * @var string
     */
    public $kind;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $extension;

    /**
     * @var string
     */
    public $filename;

    /**
     * @var Timestamp
     */
    public $dateCreatedTimestamp;

    /**
     * @var Timestamp
     */
    public $dateCreated;

    /**
     * @var Timestamp
     */
    public $dateUpdatedTimestamp;

    /**
     * @var Timestamp
     */
    public $dateUpdated;

    /**
     * @var AssetFocalPoint
     */
    public $focalPoint;

    // function boot() {
    //     $this->addStringField('url')
    //         ->use(new AssetTransformArguments);
    //     $this->addStringField('width')
    //         ->use(new AssetTransformArguments);
    //     $this->addStringField('height')
    //         ->use(new AssetTransformArguments);
    // }

    // function resolveType() {
    //     var_dump('volumeinterface!!!!');
    //     die;
    //     return function ($type) {
    //         return ucfirst($type->volume->handle).'Volume';
    //     };
    // }

}
