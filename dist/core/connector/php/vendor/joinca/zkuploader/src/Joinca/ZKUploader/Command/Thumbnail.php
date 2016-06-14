<?php


namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Error;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\Exception\ZKUploaderException;
use Joinca\ZKUploader\Exception\FileNotFoundException;
use Joinca\ZKUploader\Exception\InvalidNameException;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Filesystem\File\File;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Image;
use Joinca\ZKUploader\Thumbnail\ThumbnailRepository;
use Joinca\ZKUploader\Utils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Thumbnail extends CommandAbstract
{
    protected $requires = array(Permission::FILE_VIEW);

    public function execute(Request $request, WorkingFolder $workingFolder, Config $config, ThumbnailRepository $thumbnailRepository)
    {
        if (!$config->get('thumbnails.enabled')) {
            throw new ZKUploaderException('Thumbnails feature is disabled', Error::THUMBNAILS_DISABLED);
        }

        $fileName = (string) $request->get('fileName');

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!Image::isSupportedExtension($ext, $thumbnailRepository->isBitmapSupportEnabled())) {
            throw new InvalidNameException('Invalid source file name');
        }

        if (null === $fileName || !File::isValidName($fileName, $config->get('disallowUnsafeCharacters'))) {
            throw new InvalidRequestException('Invalid file name');
        }

        if (!$workingFolder->containsFile($fileName)) {
            throw new FileNotFoundException();
        }

        list($requestedWidth, $requestedHeight) = Image::parseSize((string) $request->get('size'));

        $thumbnail = $thumbnailRepository->getThumbnail($workingFolder->getResourceType(),
            $workingFolder->getClientCurrentFolder(), $fileName, $requestedWidth, $requestedHeight);

        Utils::removeSessionCacheHeaders();

        $response = new Response();
        $response->setPublic();
        $response->setEtag(dechex($thumbnail->getTimestamp()) . "-" . dechex($thumbnail->getSize()));

        $lastModificationDate = new \DateTime();
        $lastModificationDate->setTimestamp($thumbnail->getTimestamp());

        $response->setLastModified($lastModificationDate);

        if ($response->isNotModified($request)) {
            return $response;
        }

        $thumbnailsCacheExpires = (int) $config->get('cache.thumbnails');

        if ($thumbnailsCacheExpires > 0) {
            $response->setMaxAge($thumbnailsCacheExpires);

            $expireTime = new \DateTime();
            $expireTime->modify('+' . $thumbnailsCacheExpires . 'seconds');
            $response->setExpires($expireTime);
        }

        $response->headers->set('Content-Type', $thumbnail->getMimeType() . '; name="' . $thumbnail->getFileName() . '"');
        $response->setContent($thumbnail->getImageData());

        return $response;
    }
}
