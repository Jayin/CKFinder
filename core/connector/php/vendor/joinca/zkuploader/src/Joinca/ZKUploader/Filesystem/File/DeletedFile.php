<?php



namespace Joinca\ZKUploader\Filesystem\File;

use Joinca\ZKUploader\Error;
use Joinca\ZKUploader\Exception\InvalidExtensionException;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Filesystem\Path;

/**
 * The DeletedFile class.
 *
 * Represents the deleted file.
 */
class DeletedFile extends ExistingFile
{
    /**
     * Deletes the current file.
     *
     * @return bool `true` if the file was deleted successfully.
     *
     * @throws \Exception
     */
    public function doDelete()
    {
        if ($this->resourceType->getBackend()->delete($this->getFilePath())) {
            $this->deleteThumbnails();
            $this->deleteResizedImages();
            $this->getCache()->delete(Path::combine($this->resourceType->getName(), $this->folder, $this->getFilename()));

            return true;
        } else {
            $this->addError(Error::ACCESS_DENIED);

            return false;
        }
    }

    public function isValid()
    {
        if (!$this->hasValidFilename() || !$this->hasValidPath()) {
            throw new InvalidRequestException('Invalid filename or path');
        }

        if (!$this->hasAllowedExtension()) {
            throw new InvalidExtensionException();
        }

        if ($this->isHidden() || $this->hasHiddenPath()) {
            throw new InvalidRequestException('Deleted file is hidden');
        }

        if (!$this->exists()) {
            $this->addError(Error::FILE_NOT_FOUND);

            return false;
        }

        return true;
    }
}
