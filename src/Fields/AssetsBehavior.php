<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;
use craft\elements\Asset;
use craft\helpers\Assets;
use Craft;

class AssetsBehavior extends Behavior
{
    static $inputObjects = [];

    function getInputObject($field) {
        if (isset(static::$inputObjects[$field->handle])) {
            return static::$inputObjects[$field->handle];
        }

        return static::$inputObjects[$field->handle] = new InputObjectType([
            'name' => ucfirst($field->handle).'AssetInput',
            'fields' => [
                'id' => ['type' => Type::int()],
                'url' => ['type' => Type::string()],
            ],
        ]);
    }

    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::listOf($this->getInputObject($field))],
        ];
    }

    public function getGraphQLQueryFields($token) {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => Type::listOf(\markhuot\CraftQL\Types\Volume::interface()),
                'description' => $field->instructions,
                'resolve' => function ($root, $args) use ($field) {
                    return $root->{$field->handle}->all();
                }
            ]
        ];
    }

    public function upsert($values) {
        $images = [];
        
        foreach ($values as $value) {
            if (!empty($value['id'])) {
                $images[] = $value['id'];
            }
            
            if (!empty($value['url'])) {
                $remoteUrl = $value['url'];
                $parts = parse_url($remoteUrl);
                $filename = basename($parts['path']);
                // $basename = basename($parts['path']);
                // $filename = Assets::prepareAssetName($basename, true);

                $temp = tmpfile();
                fwrite($temp, file_get_contents($remoteUrl));
                $uploadPath = stream_get_meta_data($temp)['uri'];

                if (!pathinfo($filename, PATHINFO_EXTENSION)) {
                    $mimeType = mime_content_type($uploadPath);
                    $exts = \craft\helpers\FileHelper::getExtensionsByMimeType($mimeType);
                    if (count($exts)) {
                        $ext = $exts[count($exts)-1];
                        $filename = pathinfo($filename, PATHINFO_FILENAME).'.'.$ext;
                    }
                }

                $asset = new Asset();
                $asset->tempFilePath = $uploadPath;
                $asset->filename = $filename;
                $asset->volumeId = 1;
                $asset->newFolderId = 1;
                $asset->newFilename = $filename;
                $asset->newLocation = '{folder:1}'.$filename;
                $asset->avoidFilenameConflicts = true;
                $asset->setScenario(Asset::SCENARIO_CREATE);

                $result = Craft::$app->getElements()->saveElement($asset);
                if ($result) {
                    $images[] = $asset->id;
                }
                else {
                    throw new Exception(implode(' ', $asset->getFirstErrors()));
                }

                fclose($temp);
            }
        }

        return $images;
    }

}