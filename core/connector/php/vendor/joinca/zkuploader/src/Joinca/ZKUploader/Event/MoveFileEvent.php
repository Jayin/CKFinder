<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\File\MovedFile;

/**
 * The MoveFileEvent event class.
 */
class MoveFileEvent extends ZKUploaderEvent
{
    /**
     * @var MovedFile $movedFile
     */
    protected $movedFile;

    /**
     * Constructor.
     *
     * @param ZKUploader     $app
     * @param MovedFile    $movedFile
     */
    public function __construct(ZKUploader $app, MovedFile $movedFile)
    {
        $this->movedFile = $movedFile;

        parent::__construct($app);
    }

    /**
     * Returns the moved file object.
     *
     * @return MovedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getMovedFile()
    {
        return $this->movedFile;
    }

    /**
     * Returns the moved file object.
     *
     * @return MovedFile
     */
    public function getFile()
    {
        return $this->movedFile;
    }
}
