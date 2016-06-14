<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\File\UploadedFile;

/**
 * The FileUploadEvent event class.
 */
class FileUploadEvent extends ZKUploaderEvent
{
    /**
     * @var UploadedFile $uploadedFile
     */
    protected $uploadedFile;

    /**
     * Constructor.
     *
     * @param ZKUploader     $app
     * @param UploadedFile $uploadedFile
     */
    public function __construct(ZKUploader $app, UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        parent::__construct($app);
    }

    /**
     * Returns the uploaded file object.
     *
     * @return UploadedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * Returns the uploaded file object.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->uploadedFile;
    }
}
