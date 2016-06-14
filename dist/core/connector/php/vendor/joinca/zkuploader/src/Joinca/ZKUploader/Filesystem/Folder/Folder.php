<?php



namespace Joinca\ZKUploader\Filesystem\Folder;

use Joinca\ZKUploader\Filesystem\File\File;
use Joinca\ZKUploader\Filesystem\Path;
use Joinca\ZKUploader\ResourceType\ResourceType;

/**
 * The Folder class.
 *
 * Represents a folder in the file system.
 */
class Folder
{
    /**
     * @var ResourceType $resourceType
     */
    protected $resourceType;

    /**
     * Backend relative path (includes the resource type directory).
     *
     * @var string $path
     */
    protected $path;

    /**
     * @param ResourceType $resourceType resource type
     * @param string       $path         resource type relative path
     */
    public function __construct(ResourceType $resourceType, $path)
    {
        $this->resourceType = $resourceType;
        $this->path = Path::combine($resourceType->getDirectory(), $path);
    }

    /**
     * Checks whether `$folderName` is a valid folder name. Returns `true` on success.
     *
     * @param string $folderName
     * @param bool   $disallowUnsafeCharacters
     *
     * @return boolean
     */
    public static function isValidName($folderName, $disallowUnsafeCharacters)
    {
        if ($disallowUnsafeCharacters) {
            if (strpos($folderName, ".") !== false) {
                return false;
            }
        }

        return File::isValidName($folderName, $disallowUnsafeCharacters);
    }
}
