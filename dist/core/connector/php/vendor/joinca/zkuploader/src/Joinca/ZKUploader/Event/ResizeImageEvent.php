<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\ResizedImage\ResizedImageAbstract;

/**
 * The ResizeImageEvent class.
 */
class ResizeImageEvent extends ZKUploaderEvent
{
    /**
     * @var ResizedImageAbstract
     */
    protected $resizedImage;

    /**
     * @param ZKUploader             $app
     * @param ResizedImageAbstract $resizedImage
     */
    public function __construct(ZKUploader $app, ResizedImageAbstract $resizedImage)
    {
        parent::__construct($app);

        $this->resizedImage = $resizedImage;
    }

    /**
     * Returns the resized image object.
     *
     * @return ResizedImageAbstract
     */
    public function getResizedImage()
    {
        return $this->resizedImage;
    }

    /**
     * Sets the resized image object.
     *
     * @param ResizedImageAbstract $resizedImage
     */
    public function setResizedImage(ResizedImageAbstract $resizedImage)
    {
        $this->resizedImage = $resizedImage;
    }
}
