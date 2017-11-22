<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;
use craft\elements\Asset;
use craft\helpers\Assets;
use Craft;
use craft\base\ElementInterface;
use craft\errors\InvalidVolumeException;

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

    public function upsert($values, $entry) {
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

                // var_dump($this->owner)
                // var_dump($targetFolderId = $this->_determineUploadFolderId($element));

                $targetFolderId = $this->_determineUploadFolderId($entry);
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
        }

        return $images;
    }

    /**
     * Determine an upload folder id by looking at the settings and whether Element this field belongs to is new or not.
     *
     * @param ElementInterface|null $element
     * @param bool                  $createDynamicFolders whether missing folders should be created in the process
     *
     * @return int if the folder subpath is not valid
     * @throws InvalidSubpathException if the folder subpath is not valid
     * @throws InvalidVolumeException if there's a problem with the field's volume configuration
     */
    private function _determineUploadFolderId(ElementInterface $element = null, bool $createDynamicFolders = true): int
    {
        $field = $this->owner;

        /** @var Element $element */
        if ($field->useSingleFolder) {
            $uploadSource = $field->singleUploadLocationSource;
            $subpath = $field->singleUploadLocationSubpath;
        } else {
            $uploadSource = $field->defaultUploadLocationSource;
            $subpath = $field->defaultUploadLocationSubpath;
        }

        if (!$uploadSource) {
            throw new InvalidVolumeException(Craft::t('app', 'This field\'s Volume configuration is invalid.'));
        }

        $assets = Craft::$app->getAssets();

        try {
            $folderId = $this->_resolveVolumePathToFolderId($uploadSource, $subpath, $element, $createDynamicFolders);
        } catch (InvalidVolumeException $exception) {
            $message = $this->useSingleFolder ? Craft::t('app', 'This field’s single upload location Volume is missing') : Craft::t('app', 'This field’s default upload location Volume is missing');
            throw new InvalidVolumeException($message);
        } catch (InvalidSubpathException $exception) {
            // If this is a new/disabled element, the subpath probably just contained a token that returned null, like {id}
            // so use the user's upload folder instead
            if ($element === null || !$element->id || !$element->enabled || !$createDynamicFolders) {
                $userModel = Craft::$app->getUser()->getIdentity();

                $userFolder = $assets->getUserTemporaryUploadFolder($userModel);

                $folderId = $userFolder->id;
            } else {
                // Existing element, so this is just a bad subpath
                throw $exception;
            }
        }

        return $folderId;
    }

    /**
     * Resolve source path for uploading for this field.
     *
     * @param ElementInterface|null $element
     *
     * @return int
     */
    public function resolveDynamicPathToFolderId(ElementInterface $element = null): int
    {
        return $this->_determineUploadFolderId($element, true);
    }

    /**
     * Resolve a source path to it's folder ID by the source path and the matched source beginning.
     *
     * @param string                $uploadSource
     * @param string                $subpath
     * @param ElementInterface|null $element
     * @param bool                  $createDynamicFolders whether missing folders should be created in the process
     *
     * @throws InvalidVolumeException if the volume root folder doesn’t exist
     * @throws InvalidSubpathException if the subpath cannot be parsed in full
     * @return int
     */
    private function _resolveVolumePathToFolderId(string $uploadSource, string $subpath, ElementInterface $element = null, bool $createDynamicFolders = true): int
    {
        $assetsService = Craft::$app->getAssets();

        $volumeId = $this->_volumeIdBySourceKey($uploadSource);

        // Make sure the volume and root folder actually exists
        if ($volumeId === null || ($rootFolder = $assetsService->getRootFolderByVolumeId($volumeId)) === null) {
            throw new InvalidVolumeException();
        }

        // Are we looking for a subfolder?
        $subpath = is_string($subpath) ? trim($subpath, '/') : '';

        if ($subpath === '') {
            // Get the root folder in the source
            $folder = $rootFolder;
        } else {
            // Prepare the path by parsing tokens and normalizing slashes.
            try {
                $renderedSubpath = Craft::$app->getView()->renderObjectTemplate($subpath, $element);
            } catch (\Throwable $e) {
                throw new InvalidSubpathException($subpath);
            }

            // Did any of the tokens return null?
            if (
                $renderedSubpath === '' ||
                trim($renderedSubpath, '/') != $renderedSubpath ||
                strpos($renderedSubpath, '//') !== false
            ) {
                throw new InvalidSubpathException($subpath);
            }

            // Sanitize the subpath
            $segments = explode('/', $renderedSubpath);
            foreach ($segments as &$segment) {
                $segment = FileHelper::sanitizeFilename($segment, [
                    'asciiOnly' => Craft::$app->getConfig()->getGeneral()->convertFilenamesToAscii
                ]);
            }
            unset($segment);
            $subpath = implode('/', $segments);

            $folder = $assetsService->findFolder([
                'volumeId' => $volumeId,
                'path' => $subpath.'/'
            ]);

            // Ensure that the folder exists
            if (!$folder) {
                if (!$createDynamicFolders) {
                    throw new InvalidSubpathException($subpath);
                }

                // Start at the root, and, go over each folder in the path and create it if it's missing.
                $parentFolder = $rootFolder;

                $segments = explode('/', $subpath);
                foreach ($segments as $segment) {
                    $folder = $assetsService->findFolder([
                        'parentId' => $parentFolder->id,
                        'name' => $segment
                    ]);

                    // Create it if it doesn't exist
                    if (!$folder) {
                        $folder = $this->_createSubfolder($parentFolder, $segment);
                    }

                    // In case there's another segment after this...
                    $parentFolder = $folder;
                }
            }
        }

        return $folder->id;
    }

    /**
     * Returns a volume ID from an upload source key.
     *
     * @param string $sourceKey
     *
     * @return int|null
     */
    public function _volumeIdBySourceKey(string $sourceKey)
    {
        $parts = explode(':', $sourceKey, 2);

        if (count($parts) !== 2 || !is_numeric($parts[1])) {
            return null;
        }

        $folder = Craft::$app->getAssets()->getFolderById((int)$parts[1]);

        return $folder->volumeId ?? null;
    }

}