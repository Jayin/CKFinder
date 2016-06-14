<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\File\DownloadedFile;

/**
 * The DownloadFileEvent event class.
 */
class DownloadFileEvent extends ZKUploaderEvent
{
    /**
     * @var DownloadedFile $downloadedFile
     */
    protected $downloadedFile;

    /**
     * Constructor.
     *
     * @param ZKUploader       $app
     * @param DownloadedFile $downloadedFile
     */
    public function __construct(ZKUploader $app, DownloadedFile $downloadedFile)
    {
        $this->downloadedFile = $downloadedFile;

        parent::__construct($app);
    }

    /**
     * Returns the downloaded file object.
     *
     * @return DownloadedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getDownloadedFile()
    {
        return $this->downloadedFile;
    }

    /**
     * Returns the downloaded file object.
     *
     * @return DownloadedFile
     */
    public function getFile()
    {
        return $this->downloadedFile;
    }
}
