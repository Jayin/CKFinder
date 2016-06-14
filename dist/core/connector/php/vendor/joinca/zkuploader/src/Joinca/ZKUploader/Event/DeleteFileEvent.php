<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\File\DeletedFile;

/**
 * The DeleteFileEvent event class.
 */
class DeleteFileEvent extends ZKUploaderEvent
{
    /**
     * @var DeletedFile $deletedFile
     */
    protected $deletedFile;

    /**
     * Constructor.
     *
     * @param ZKUploader      $app
     * @param DeletedFile   $deletedFile
     */
    public function __construct(ZKUploader $app, DeletedFile $deletedFile)
    {
        $this->deletedFile = $deletedFile;

        parent::__construct($app);
    }

    /**
     * Returns the deleted file object.
     *
     * @return DeletedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getDeletedFile()
    {
        return $this->deletedFile;
    }

    /**
     * Returns the deleted file object.
     *
     * @return DeletedFile
     */
    public function getFile()
    {
        return $this->deletedFile;
    }
}
