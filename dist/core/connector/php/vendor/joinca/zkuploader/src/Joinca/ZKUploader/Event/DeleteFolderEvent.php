<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;

/**
 * The DeleteFolderEvent event class.
 */
class DeleteFolderEvent extends ZKUploaderEvent
{
    /**
     * The working folder that is going to be deleted.
     *
     * @var WorkingFolder $workingFolder
     */
    protected $workingFolder;

    /**
     * Constructor.
     *
     * @param ZKUploader      $app
     * @param WorkingFolder $workingFolder
     */
    public function __construct(ZKUploader $app, WorkingFolder $workingFolder)
    {
        $this->workingFolder = $workingFolder;

        parent::__construct($app);
    }

    /**
     * Returns the working folder that is going to be deleted.
     *
     * @return WorkingFolder
     */
    public function getWorkingFolder()
    {
        return $this->workingFolder;
    }
}
