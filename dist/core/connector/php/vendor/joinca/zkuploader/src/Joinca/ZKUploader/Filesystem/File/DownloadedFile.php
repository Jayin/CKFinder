<?php



namespace Joinca\ZKUploader\Filesystem\File;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Exception\FileNotFoundException;
use Joinca\ZKUploader\Exception\InvalidExtensionException;
use Joinca\ZKUploader\Exception\InvalidNameException;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;

/**
 * The DownloadedFile class.
 *
 * Represents downloaded file
 */
class DownloadedFile extends ExistingFile
{
    /**
     * @var WorkingFolder $workingFolder
     */
    protected $workingFolder;

    /**
     * Constructor.
     *
     * @param string        $fileName
     * @param ZKUploader      $app
     */
    public function __construct($fileName, ZKUploader $app)
    {
        $this->workingFolder = $app['working_folder'];

        parent::__construct($fileName, $this->workingFolder->getClientCurrentFolder(), $this->workingFolder->getResourceType(), $app);
    }

    /**
     * Returns the folder of the downloaded file.
     *
     * @return WorkingFolder
     */
    public function getWorkingFolder()
    {
        return $this->workingFolder;
    }

    /**
     * Validates the downloaded file.
     *
     * @throws \Exception
     *
     * @return boolean `true` if the file passed validation.
     */
    public function isValid()
    {
        if (!$this->hasValidFilename()) {
            throw new InvalidNameException('Invalid file name');
        }

        if (!$this->hasAllowedExtension()) {
            throw new InvalidExtensionException();
        }

        if ($this->isHidden() || !$this->exists()) {
            throw new FileNotFoundException();
        }

        return true;
    }

    /**
     * Checks if the file extension is allowed.
     *
     * @return bool `true` if an extension is allowed.
     */
    public function hasAllowedExtension()
    {
        if (strpos($this->fileName, '.') === false) {
            return true;
        }

        $extension = $this->getExtension();

        return $this->workingFolder->getResourceType()->isAllowedExtension($extension);
    }

    /**
     * Checks if the file is hidden.
     *
     * @return bool `true` if the file is hidden.
     */
    public function isHidden()
    {
        return $this->workingFolder->getBackend()->isHiddenFile($this->fileName);
    }

    /**
     * Checks if the file exists.
     *
     * @return bool `true` if the file exists.
     */
    public function exists()
    {
        return $this->workingFolder->containsFile($this->fileName);
    }
}
