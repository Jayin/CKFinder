<?php



namespace Joinca\ZKUploader\Filesystem\File;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Exception\AlreadyExistsException;
use Joinca\ZKUploader\Exception\FileNotFoundException;
use Joinca\ZKUploader\Exception\InvalidExtensionException;
use Joinca\ZKUploader\Exception\InvalidNameException;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Filesystem\Path;
use Joinca\ZKUploader\ResourceType\ResourceType;
use Joinca\ZKUploader\Utils;

/**
 * The RenamedFile class.
 *
 * Represents the file being renamed.
 */
class RenamedFile extends ExistingFile
{
    /**
     * New file name.
     *
     * @var string $newFileName
     */
    protected $newFileName;

    /**
     * Constructor.
     *
     * @param string       $newFileName  new file name
     * @param string       $fileName     current file name
     * @param string       $folder       current file folder
     * @param ResourceType $resourceType current file resource type
     * @param ZKUploader     $app          ZKUploader app
     */
    public function __construct($newFileName, $fileName, $folder, ResourceType $resourceType, ZKUploader $app)
    {
        parent::__construct($fileName, $folder, $resourceType, $app);

        $this->newFileName = static::secureName($newFileName, $this->config->get('disallowUnsafeCharacters'));

        if ($this->config->get('checkDoubleExtension')) {
            $this->newFileName = Utils::replaceDisallowedExtensions($this->newFileName, $resourceType);
        }
    }

    /**
     * Returns the new file name of the renamed file.
     *
     * @return string
     */
    public function getNewFileName()
    {
        return $this->newFileName;
    }

    /**
     * Returns the new path of the renamed file.
     *
     * @return string
     */
    public function getNewFilePath()
    {
        return Path::combine($this->getPath(), $this->getNewFileName());
    }

    /**
     * Sets the new file name of the renamed file.
     *
     * @param string $newFileName
     */
    public function setNewFileName($newFileName)
    {
        $this->newFileName = $newFileName;
    }

    /**
     * Renames the current file.
     *
     * @return bool `true` if the file was renamed successfully.
     *
     * @throws \Exception
     */
    public function doRename()
    {
        $oldPath = Path::combine($this->getPath(), $this->getFilename());
        $newPath = Path::combine($this->getPath(), $this->newFileName);

        $backend = $this->resourceType->getBackend();

        if ($backend->has($newPath)) {
            throw new AlreadyExistsException('Target file already exists');
        }

        $this->deleteThumbnails();
        $this->resourceType->getResizedImageRepository()->renameResizedImages(
            $this->resourceType,
            $this->folder,
            $this->getFilename(),
            $this->newFileName
        );

        $this->getCache()->move(
            Path::combine($this->resourceType->getName(), $this->folder, $this->getFilename()),
            Path::combine($this->resourceType->getName(), $this->folder, $this->newFileName));

        return $backend->rename($oldPath, $newPath);
    }

    /**
     * Validates the renamed file.
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isValid()
    {
        $newExtension = pathinfo($this->newFileName, PATHINFO_EXTENSION);

        if (!$this->hasAllowedExtension()) {
            throw new InvalidRequestException('Invalid source file extension');
        }

        if (!$this->resourceType->isAllowedExtension($newExtension)) {
            throw new InvalidExtensionException('Invalid target file extension');
        }

        if (!$this->hasValidFilename() || $this->isHidden()) {
            throw new InvalidRequestException('Invalid source file name');
        }

        if (!File::isValidName($this->newFileName, $this->config->get('disallowUnsafeCharacters')) ||
            $this->resourceType->getBackend()->isHiddenFile($this->newFileName)) {
            throw new InvalidNameException('Invalid target file name');
        }

        if (!$this->exists()) {
            throw new FileNotFoundException();
        }

        return true;
    }
}
