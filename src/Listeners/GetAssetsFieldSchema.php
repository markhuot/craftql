<?php

namespace markhuot\CraftQL\Listeners;

use Craft;
use craft\elements\Asset;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;
use markhuot\CraftQL\FieldBehaviors\AssetQueryArguments;
use markhuot\CraftQL\Types\Volume;
use markhuot\CraftQL\Types\VolumeInterface;
use markhuot\CraftQL\Events\GetFieldSchema;

class GetAssetsFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle(GetFieldSchema $event) {
        $event->handled = true;

        $field = $event->sender;
        $schema = $event->schema;

        $schema->addField($field)
            ->lists()
            ->type(VolumeInterface::class)
            ->use(new AssetQueryArguments)
            ->resolve(function ($root, $args) use ($field) {
                return $root->{$field->handle}->all();
            });

        $inputObject = $event->mutation->createInputObjectType(ucfirst($field->handle).'AssetInput');
        $inputObject->addIntArgument('id');
        $inputObject->addStringArgument('url');
        $inputObject->addStringArgument('title');
        // $inputObject->addFieldsByLayoutId();

        $event->mutation->addArgument($field)
            ->lists()
            ->type($inputObject)
            ->onSave(function ($values) {
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
                    if (!empty($value['title'])) {
                        $asset->title = $value['title'];
                    }
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
            });
    }
}
