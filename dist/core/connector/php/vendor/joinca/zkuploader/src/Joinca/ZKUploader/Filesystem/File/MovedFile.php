<?php



namespace Joinca\ZKUploader\Filesystem\File;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\Path;
use Joinca\ZKUploader\ResourceType\ResourceType;

/**
 * The MovedFile class.
 *
 * Represents the moved file.
 */
class MovedFile extends CopiedFile
{
    /**
     * Constructor.
     *
     * @param string       $fileName     source file name
     * @param string       $folder       source file resource type relative path
     * @param ResourceType $resourceType source file resource type
     * @param ZKUploader     $app          app
     */
    public function __construct($fileName, $folder, ResourceType $resourceType, ZKUploader $app)
    {
        parent::__construct($fileName, $folder, $resourceType, $app);
    }

    /**
     * Moves the current file.
     *
     * @return bool `true` if the file was moved successfully.
     *
     * @throws \Exception
     */
    public function doMove()
    {
        $originalFilePath = $this->getFilePath();
        $originalFileName = $this->getFilename(); // Save original file name - it may be autorenamed when copied

        if (parent::doCopy()) {
            // Remove source file
            $this->deleteThumbnails();
            $this->resourceType->getResizedImageRepository()->deleteResizedImages($this->resourceType, $this->folder, $originalFileName);
            $this->getCache()->delete(Path::combine($this->resourceType->getName(), $this->folder, $originalFileName));

            return $this->resourceType->getBackend()->delete($originalFilePath);
        }

        return false;
    }
}
