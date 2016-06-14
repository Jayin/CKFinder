<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\File\DownloadedFile;
use Joinca\ZKUploader\ResizedImage\ResizedImage;

/**
 * The DownloadFileEvent event class.
 */
class ProxyDownloadEvent extends ZKUploaderEvent
{
    /**
     * @var DownloadedFile|ResizedImage $downloadedFile
     */
    protected $downloadedFile;

    /**
     * Constructor.
     *
     * @param ZKUploader                    $app
     * @param DownloadedFile|ResizedImage $downloadedFile
     */
    public function __construct(ZKUploader $app, $downloadedFile)
    {
        $this->downloadedFile = $downloadedFile;

        parent::__construct($app);
    }

    /**
     * Returns the downloaded file object.
     *
     * @return DownloadedFile|ResizedImage
     */
    public function getFile()
    {
        return $this->downloadedFile;
    }
}
