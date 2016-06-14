<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\File\CopiedFile;

/**
 * The CopyFileEvent event class.
 */
class CopyFileEvent extends ZKUploaderEvent
{
    /**
     * @var CopiedFile $copiedFile
     */
    protected $copiedFile;

    /**
     * Constructor.
     *
     * @param ZKUploader     $app
     * @param CopiedFile   $copiedFile
     */
    public function __construct(ZKUploader $app, CopiedFile $copiedFile)
    {
        $this->copiedFile = $copiedFile;

        parent::__construct($app);
    }

    /**
     * Returns the copied file object.
     *
     * @return CopiedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getCopiedFile()
    {
        return $this->copiedFile;
    }

    /**
     * Returns the copied file object.
     *
     * @return CopiedFile
     */
    public function getFile()
    {
        return $this->copiedFile;
    }
}
