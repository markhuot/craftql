<?php

namespace markhuot\CraftQL\Listeners;

use Craft;
use craft\elements\Asset;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;
use markhuot\CraftQL\Types\Volume;

class GetAssetsFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    static function handle($event) {
        $event->handled = true;

        $field = $event->sender;
        $builder = $event->builder;

        $builder
            ->addCraftField(
                $field,
                Type::listOf(Volume::interface()),
                function ($root, $args) use ($field) {
                    return $root->{$field->handle}->all();
                }
            )
            ->addCraftArgument(
                $field,
                Type::listOf(static::getInputObject($field)),
                [static::class, 'upload']
            );
    }

    /**
     * Do the upload
     *
     * @param Element $entry
     * @param Field $field
     * @param array $values
     * @return array The element ids of the uploaded files
     */
    static function upload($entry, $field, $values) {
        $images = [];

        foreach ($values as $value) {
            if (!empty($value['id'])) {
                $images[] = $value['id'];
                continue;
            }

            $remoteUrl = $value['url'];
            $parts = parse_url($remoteUrl);
            $filename = basename($parts['path']);

            $uploadPath = \craft\helpers\Assets::tempFilePath();
            file_put_contents($uploadPath, file_get_contents($remoteUrl));

            if (!pathinfo($filename, PATHINFO_EXTENSION)) {
                $mimeType = mime_content_type($uploadPath);
                $exts = \craft\helpers\FileHelper::getExtensionsByMimeType($mimeType);
                if (count($exts)) {
                    $ext = $exts[count($exts)-1];
                    $filename = pathinfo($filename, PATHINFO_FILENAME).'.'.$ext;
                }
            }

            $targetFolderId = 1;
            $folder = Craft::$app->getAssets()->getFolderById($targetFolderId);

            $asset = new Asset();
            $asset->tempFilePath = $uploadPath;
            $asset->filename = $filename;
            $asset->newFolderId = $targetFolderId;
            $asset->volumeId = $folder->volumeId;
            $asset->avoidFilenameConflicts = true;
            $asset->setScenario(Asset::SCENARIO_CREATE);

            $result = Craft::$app->getElements()->saveElement($asset);
            if ($result) {
                $images[] = $asset->id;
            }
            else {
                throw new \Exception(implode(' ', $asset->getFirstErrors()));
            }
        }

        return $images;
    }

    /**
     * The input object. There are unique input objects per volume because
     * each volume can have different fields
     *
     * @param Field $field
     * @return InputObjectType
     */
    static function getInputObject($field) {
        return new InputObjectType([
            'name' => ucfirst($field->handle).'AssetInput',
            'fields' => [
                'id' => ['type' => Type::int()],
                'url' => ['type' => Type::string()],
            ],
        ]);
    }
}
