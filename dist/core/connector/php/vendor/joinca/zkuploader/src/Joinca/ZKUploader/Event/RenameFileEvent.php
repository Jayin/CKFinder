<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\File\RenamedFile;

/**
 * The RenameFileEvent event class.
 */
class RenameFileEvent extends ZKUploaderEvent
{
    /**
     * @var RenamedFile $renamedFile
     */
    protected $renamedFile;

    /**
     * Constructor.
     *
     * @param ZKUploader    $app
     * @param RenamedFile $renamedFile
     */
    public function __construct(ZKUploader $app, RenamedFile $renamedFile)
    {
        $this->renamedFile = $renamedFile;

        parent::__construct($app);
    }

    /**
     * Returns the renamed file object.
     *
     * @return RenamedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getRenamedFile()
    {
        return $this->renamedFile;
    }

    /**
     * Returns the renamed file object.
     *
     * @return RenamedFile
     */
    public function getFile()
    {
        return $this->renamedFile;
    }
}
