<?php


namespace Joinca\ZKUploader\Thumbnail;

use Joinca\ZKUploader\Exception\FileNotFoundException;
use Joinca\ZKUploader\Filesystem\Path;
use Joinca\ZKUploader\Image;
use Joinca\ZKUploader\ResourceType\ResourceType;
use Joinca\ZKUploader\ResizedImage\ResizedImage;
use Joinca\ZKUploader\ResizedImage\ResizedImageAbstract;

/**
 * The Thumbnail class.
 *
 * A class representing a thumbnail.
 *
 * 
 */
class Thumbnail extends ResizedImageAbstract
{
    /**
     * @var ThumbnailRepository $thumbnailRepository
     */
    protected $thumbnailRepository;


    /**
     * An array containing adjusted size info for this thumbnail.
     *
     * Dimensions passed in `$requestedWidth` and `$requestedHeight`
     * are adjusted to one of the allowed sizes. The smallest allowed
     * thumbnail size that is bigger than the requested one is used.
     *
     * Example array stored in this attribute:
     *
     *     array('width' => '150', 'height' => '150', 'quality' => 80)
     *
     * @var array $adjustedSizeInfo
     */
    protected $adjustedSizeInfo;

    /**
     * @param ThumbnailRepository $thumbnailRepository
     * @param ResourceType        $sourceFileResourceType
     * @param string              $sourceFileDir
     * @param string              $sourceFileName
     * @param int                 $requestedWidth
     * @param int                 $requestedHeight
     */
    public function __construct(ThumbnailRepository $thumbnailRepository, ResourceType $sourceFileResourceType, $sourceFileDir, $sourceFileName, $requestedWidth, $requestedHeight)
    {
        parent::__construct($sourceFileResourceType, $sourceFileDir, $sourceFileName, $requestedWidth, $requestedHeight);

        $this->thumbnailRepository = $thumbnailRepository;

        $this->adjustDimensions();
        $this->backend = $thumbnailRepository->getThumbnailBackend();

        $width = $this->adjustedSizeInfo['width'];
        $height = $this->adjustedSizeInfo['height'];

        $this->resizedImageFileName = ResizedImage::createFilename($sourceFileName, $width, $height);
    }

    /**
     * Adjusts thumbnail dimensions.
     *
     * Dimensions passed in `$requestedWidth` and `$requestedHeight`
     * are adjusted to one of the allowed sizes. The smallest allowed
     * thumbnail size that is bigger than the requested one is used.
     */
    protected function adjustDimensions()
    {
        $allowedSizes = $this->thumbnailRepository->getAllowedSizes();

        $this->adjustedSizeInfo = end($allowedSizes);

        foreach ($allowedSizes as $sizeInfo) {
            if ($sizeInfo['width'] >= $this->requestedWidth && $sizeInfo['height'] >= $this->requestedHeight) {
                $this->adjustedSizeInfo = $sizeInfo;
                break;
            }
        }
    }

    /**
     * Returns backend-relative thumbnails directory.
     *
     * @return string
     */
    public function getDirectory()
    {
        return Path::combine(
            $this->thumbnailRepository->getThumbnailsPath(),
            $this->sourceFileResourceType->getName(),
            $this->sourceFileDir,
            $this->sourceFileName);
    }

    /**
     * Creates a thumbnail.
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function create()
    {
        $sourceBackend = $this->sourceFileResourceType->getBackend();
        $sourceFilePath = Path::combine($this->sourceFileResourceType->getDirectory(), $this->sourceFileDir, $this->sourceFileName);

        if ($sourceBackend->isHiddenFile($this->sourceFileName) || !$sourceBackend->has($sourceFilePath)) {
            throw new FileNotFoundException('Thumbnail::create(): Source file not found');
        }

        $image = Image::create($sourceBackend->read($sourceFilePath), $this->thumbnailRepository->isBitmapSupportEnabled());

        // Update cached info about image
        $app = $this->thumbnailRepository->getContainer();
        $app['cache']->set(
            Path::combine($this->sourceFileResourceType->getName(), $this->sourceFileDir, $this->sourceFileName),
            $image->getInfo()
        );

        $image->resize($this->adjustedSizeInfo['width'], $this->adjustedSizeInfo['height'], $this->adjustedSizeInfo['quality']);

        $this->resizedImageData = $image->getData();
        $this->resizedImageSize = $image->getDataSize();
        $this->resizedImageMimeType = $image->getMimeType();

        unset($image);
    }
}
