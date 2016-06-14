<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;

/**
 * The RenameFolderEvent event class.
 */
class RenameFolderEvent extends ZKUploaderEvent
{
    /**
     * Working folder where the new folder is going to be renamed.
     *
     * @var WorkingFolder $workingFolder
     */
    protected $workingFolder;

    /**
     * The new folder name.
     *
     * @var string
     */
    protected $newFolderName;

    /**
     * Constructor.
     *
     * @param ZKUploader      $app
     * @param WorkingFolder $workingFolder
     * @param string        $newFolderName
     */
    public function __construct(ZKUploader $app, WorkingFolder $workingFolder, $newFolderName)
    {
        $this->workingFolder = $workingFolder;
        $this->newFolderName = $newFolderName;

        parent::__construct($app);
    }

    /**
     * Returns the working folder where the new folder is going to be renamed.
     *
     * @return WorkingFolder
     */
    public function getWorkingFolder()
    {
        return $this->workingFolder;
    }

    /**
     * Returns the new name of the folder.
     *
     * @return string
     */
    public function getNewFolderName()
    {
        return $this->newFolderName;
    }

    /**
     * Sets the new name for the folder.
     *
     * @param string $newFolderName
     */
    public function setNewFolderName($newFolderName)
    {
        $this->newFolderName = $newFolderName;
    }
}
