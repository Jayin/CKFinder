<?php


namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Cache\CacheManager;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\Exception\InvalidExtensionException;
use Joinca\ZKUploader\Filesystem\File\DownloadedFile;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Filesystem\Path;
use Joinca\ZKUploader\Image;
use Joinca\ZKUploader\ResizedImage\ResizedImageRepository;
use Joinca\ZKUploader\Utils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The ImagePreview command class.
 *
 * This command produces a resized copy of the image that
 * fits requested maximum dimensions.
 *
 */
class ImagePreview extends CommandAbstract
{
    protected $requires = array(Permission::FILE_VIEW);

    public function execute(Request $request, Config $config, WorkingFolder $workingFolder, ResizedImageRepository $resizedImageRepository, CacheManager $cache)
    {
        $fileName = (string) $request->query->get('fileName');
        list($requestedWidth, $requestedHeight) = Image::parseSize((string) $request->get('size'));

        $downloadedFile = new DownloadedFile($fileName, $this->app);
        $downloadedFile->isValid();

        if (!Image::isSupportedExtension(pathinfo($fileName, PATHINFO_EXTENSION), $config->get('thumbnails.bmpSupported'))) {
            throw new InvalidExtensionException('Unsupported image type or not image file');
        }

        Utils::removeSessionCacheHeaders();

        $response = new Response();
        $response->setPublic();
        $response->setEtag(dechex($downloadedFile->getTimestamp()) . "-" . dechex($downloadedFile->getSize()));

        $lastModificationDate = new \DateTime();
        $lastModificationDate->setTimestamp($downloadedFile->getTimestamp());

        $response->setLastModified($lastModificationDate);

        if ($response->isNotModified($request)) {
            return $response;
        }

        $imagePreviewCacheExpires = (int) $config->get('cache.imagePreview');

        if ($imagePreviewCacheExpires > 0) {
            $response->setMaxAge($imagePreviewCacheExpires);

            $expireTime = new \DateTime();
            $expireTime->modify('+' . $imagePreviewCacheExpires . 'seconds');
            $response->setExpires($expireTime);
        }

        $cachedInfoPath = Path::combine(
            $workingFolder->getResourceType()->getName(),
            $workingFolder->getClientCurrentFolder(),
            $fileName
        );

        $cachedInfo = $cache->get($cachedInfoPath);

        $resultImage = null;

        // Try to reuse existing resized image
        if ($cachedInfo && isset($cachedInfo['width']) && isset($cachedInfo['height'])) {
            // Fix received aspect ratio
            $size = Image::calculateAspectRatio($requestedWidth, $requestedHeight, $cachedInfo['width'], $cachedInfo['height']);
            $resizedImage = $resizedImageRepository->getResizedImageBySize(
                $workingFolder->getResourceType(),
                $workingFolder->getClientCurrentFolder(),
                $fileName,
                $size['width'],
                $size['height']
            );
            if ($resizedImage) {
                $resultImage = Image::create($resizedImage->getImageData());
            }
        }

        // Fallback - get and resize the original image
        if (null === $resultImage) {
            $resultImage = Image::create($downloadedFile->getContents(), $config->get('thumbnails.bmpSupported'));
            $cache->set($cachedInfoPath, $resultImage->getInfo());
            $resultImage->resize($requestedWidth, $requestedHeight);
        }

        $mimeType = $resultImage->getMimeType();

        if (in_array($mimeType, array('image/bmp', 'image/x-ms-bmp'))) {
            $mimeType = 'image/jpeg'; // Image::getData() by default converts resized images to JPG
        }

        $response->headers->set('Content-Type', $mimeType. '; name="' . $downloadedFile->getFileName() . '"');
        $response->setContent($resultImage->getData());

        return $response;
    }
}
