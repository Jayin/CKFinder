<?php



namespace Joinca\ZKUploader\Filesystem\File;

use Joinca\ZKUploader\Backend\Backend;
use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Error;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Filesystem\Path;
use Joinca\ZKUploader\ResourceType\ResourceType;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;

/**
 * The CopiedFile class.
 *
 * Represents a copied file.
 */
class CopiedFile extends ExistingFile
{
    /**
     * @var WorkingFolder
     */
    protected $targetFolder;

    /**
     * @var string $copyOptions defines copy options in case a file already exists
     *                          in the target directory:
     *                          - autorename - Renames the current file (see File::autorename()).
     *                          - overwrite - Overwrites the existing file.
     */
    protected $copyOptions;

    /**
     * File name of the source file.
     *
     * @var string
     */
    protected $sourceFileName;

    /**
     * Constructor.
     *
     * @param string       $fileName     source file name
     * @param string       $folder       copied source file resource type relative path
     * @param ResourceType $resourceType source file resource type
     * @param ZKUploader     $app          ZKUploader
     */
    public function __construct($fileName, $folder, ResourceType $resourceType, ZKUploader $app)
    {
        $this->targetFolder = $app['working_folder'];

        $this->sourceFileName = $fileName;

        parent::__construct($fileName, $folder, $resourceType, $app);
    }

    /**
     * Returns the target folder for a copied file.
     *
     * @return WorkingFolder
     */
    public function getTargetFolder()
    {
        return $this->targetFolder;
    }

    public function getFileName()
    {
        return $this->sourceFileName;
    }

    /**
     * Sets copy options.
     *
     * @param string $copyOptions
     *
     * @see CopiedFile::$copyOptions
     */
    public function setCopyOptions($copyOptions)
    {
        $this->copyOptions = $copyOptions;
    }

    /**
     * Checks if the file has an extension allowed in both source and target ResourceTypes.
     *
     * @return bool `true` if the file has an extension allowed in source and target directories.
     */
    public function hasAllowedExtension()
    {
        if (strpos($this->fileName, '.') === false) {
            return true;
        }

        $extension = $this->getExtension();

        return parent::hasAllowedExtension() &&
               $this->targetFolder->getResourceType()->isAllowedExtension($extension);
    }

    /**
     * Checks if the copied file size does not exceed the file size limit set for the target folder.
     *
     * @return bool
     */
    public function hasAllowedSize()
    {
        $filePath = $this->getFilePath();
        $backend = $this->resourceType->getBackend();

        if (!$backend->has($filePath)) {
            return false;
        }

        $fileMetadata = $backend->getMetadata($filePath);

        $fileSize = $fileMetadata['size'];

        $maxSize = $this->targetFolder->getResourceType()->getMaxSize();

        if ($maxSize && $fileSize > $maxSize) {
            return false;
        }

        return true;
    }

    /**
     * @copydoc File::autorename()
     */
    public function autorename(Backend $backend = null, $path = '')
    {
        return parent::autorename($this->targetFolder->getBackend(), $this->targetFolder->getPath());
    }

    /**
     * Copies the current file.
     *
     * @return bool `true` if the file was copied successfully.
     *
     * @throws \Exception
     */
    public function doCopy()
    {
        $originalFileStream = $this->getContentsStream();

        // Don't copy file to itself
        if ($this->targetFolder->getBackend() === $this->resourceType->getBackend() &&
            $this->targetFolder->getPath() === $this->getPath()) {
            $this->addError(Error::SOURCE_AND_TARGET_PATH_EQUAL);

            return false;
        }

        $targetFilename = $this->getTargetFilename();

        if ($this->targetFolder->containsFile($targetFilename) && strpos($this->copyOptions, 'overwrite') === false) {
            $this->addError(Error::ALREADY_EXIST);

            return false;
        }

        if ($this->targetFolder->putStream($targetFilename, $originalFileStream)) {
            $resizedImageRepository = $this->resourceType->getResizedImageRepository();
            $resizedImageRepository->copyResizedImages(
                $this->resourceType, $this->folder, $this->sourceFileName,
                $this->targetFolder->getResourceType(), $this->targetFolder->getClientCurrentFolder(), $targetFilename
            );

            $this->getCache()->copy(
                Path::combine($this->resourceType->getName(), $this->folder, $this->sourceFileName),
                Path::combine($this->targetFolder->getResourceType()->getName(), $this->targetFolder->getClientCurrentFolder(), $targetFilename)
            );

            return true;
        } else {
            $this->addError(Error::ACCESS_DENIED);

            return false;
        }
    }

    /**
     * Returns the target file name of the copied file.
     *
     * @return string
     */
    public function getTargetFilename()
    {
        if ($this->targetFolder->containsFile($this->getFilename()) &&
            strpos($this->copyOptions, 'overwrite') === false &&
            strpos($this->copyOptions, 'autorename') !== false) {
            $this->autorename();
        }

        return $this->fileName;
    }

    /**
     * Returns the source file name of the copied file.
     *
     * @return string
     */
    public function getSourceFilename()
    {
        return $this->sourceFileName;
    }

    /**
     * Returns the target path of the copied file.
     *
     * @return string
     */
    public function getTargetFilePath()
    {
        return Path::combine($this->getTargetFolder()->getPath(), $this->getTargetFilename());
    }

    /**
     * Returns the source file name of the copied file.
     *
     * @return string
     */
    public function getSourceFilePath()
    {
        return Path::combine($this->getPath(), $this->sourceFileName);
    }

    /**
     * Validates the copied file.
     *
     * @return bool `true` if the copied file is valid and ready to be copied.
     *
     * @throws \Exception
     */
    public function isValid()
    {
        if (!$this->hasValidFilename() || !$this->hasValidPath()) {
            throw new InvalidRequestException('Invalid filename or path');
        }

        if (!$this->hasAllowedExtension()) {
            $this->addError(Error::INVALID_EXTENSION);

            return false;
        }

        if ($this->isHidden() || $this->hasHiddenPath()) {
            throw new InvalidRequestException('Copied file is hidden');
        }

        if (!$this->exists()) {
            $this->addError(Error::FILE_NOT_FOUND);

            return false;
        }

        if (!$this->hasAllowedSize()) {
            $this->addError(Error::UPLOADED_TOO_BIG);

            return false;
        }

        return true;
    }
}
