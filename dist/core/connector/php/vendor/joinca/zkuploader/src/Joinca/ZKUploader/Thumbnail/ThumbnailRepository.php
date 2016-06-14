<?php


namespace Joinca\ZKUploader\Thumbnail;

use Joinca\ZKUploader\Backend\Backend;
use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\ResizeImageEvent;
use Joinca\ZKUploader\Filesystem\Path;
use Joinca\ZKUploader\ResourceType\ResourceType;

/**
 * The ThumbnailRepository class.
 *
 * A class responsible for thumbnail management.
 *
 * 
 */
class ThumbnailRepository
{
    /**
     * @var ZKUploader
     */
    protected $app;

    /**
     * @var Config
     */
    protected $config;

    /**
     * The Backend where thumbnails are stored.
     *
     * @var Backend $thumbsBackend
     */
    protected $thumbsBackend;

    /**
     * Event dispatcher.
     *
     * @var $dispatcher
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param ZKUploader $app
     */
    public function __construct(ZKUploader $app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->thumbsBackend = $app['backend_factory']->getPrivateDirBackend('thumbs');
        $this->dispatcher = $app['dispatcher'];
    }

    /**
     * Returns the Backend object where thumbnails are stored.
     *
     * @return Backend
     */
    public function getThumbnailBackend()
    {
        return $this->thumbsBackend;
    }

    /**
     * @return ZKUploader
     */
    public function getContainer()
    {
        return $this->app;
    }

    /**
     * Returns backend-relative directory path where
     * thumbnails are stored.
     *
     * @return string
     */
    public function getThumbnailsPath()
    {
        return $this->config->getPrivateDirPath('thumbs');
    }

    /**
     * Returns an array of allowed sizes for thumbnails.
     *
     * @return array
     */
    public function getAllowedSizes()
    {
        return $this->config->get('thumbnails.sizes');
    }

    /**
     * Returns information about bitmap support for thumbnails. If bitmap
     * support is disabled, thumbnails for bitmaps will not be generated.
     *
     * @return bool `true` if bitmap support is enabled.
     */
    public function isBitmapSupportEnabled()
    {
        return $this->config->get('thumbnails.bmpSupported');
    }

    /**
     * Returns a thumbnail object for a given file defined by the resource type,
     * path and file name.
     * The real size of the thumbnail image will be adjusted to one of the sizes
     * allowed by the thumbnail configuration.
     *
     * @param ResourceType $resourceType    source file resource type
     * @param string       $path            source file directory path
     * @param string       $fileName        source file name
     * @param int          $requestedWidth  requested thumbnail height
     * @param int          $requestedHeight requested thumbnail height
     *
     * @return Thumbnail
     *
     * @throws \Exception
     */
    public function getThumbnail(ResourceType $resourceType, $path, $fileName, $requestedWidth, $requestedHeight)
    {
        $thumbnail = new Thumbnail($this, $resourceType, $path, $fileName, $requestedWidth, $requestedHeight);

        if (!$thumbnail->exists()) {
            $thumbnail->create();

            $createThumbnailEvent = new ResizeImageEvent($this->app, $thumbnail);
            $this->dispatcher->dispatch(ZKUploaderEvent::CREATE_THUMBNAIL, $createThumbnailEvent);

            if (!$createThumbnailEvent->isPropagationStopped()) {
                $thumbnail = $createThumbnailEvent->getResizedImage();
                $thumbnail->save();
            }
        } else {
            $thumbnail->load();
        }

        return $thumbnail;
    }

    /**
     * Deletes all thumbnails under the given path defined by the resource type,
     * path and file name.
     *
     * @param ResourceType $resourceType
     * @param string       $path
     * @param string       $fileName
     *
     * @return bool `true` if deleted successfully
     */
    public function deleteThumbnails(ResourceType $resourceType, $path, $fileName = null)
    {
        $path = Path::combine($this->getThumbnailsPath(), $resourceType->getName(), $path, $fileName);

        if ($this->thumbsBackend->has($path)) {
            return $this->thumbsBackend->deleteDir($path);
        }

        return false;
    }
}
