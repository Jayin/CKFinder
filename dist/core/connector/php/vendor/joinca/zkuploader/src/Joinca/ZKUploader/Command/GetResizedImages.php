<?php


namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Cache\CacheManager;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Filesystem\Path;
use Joinca\ZKUploader\Image;
use Joinca\ZKUploader\ResizedImage\ResizedImageRepository;
use Joinca\ZKUploader\Config;
use Symfony\Component\HttpFoundation\Request;

class GetResizedImages extends CommandAbstract
{
    protected $requires = array(Permission::FILE_VIEW);

    public function execute(Request $request, WorkingFolder $workingFolder, ResizedImageRepository $resizedImageRepository, Config $config, CacheManager $cache)
    {
        $fileName = (string) $request->get('fileName');
        $sizes = (string) $request->get('sizes');

        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        if (!Image::isSupportedExtension($ext)) {
            throw new InvalidRequestException('Invalid file extension');
        }

        if ($sizes) {
            $sizes = explode(',', $sizes);
            if (array_diff($sizes, array_keys($config->get('images.sizes')))) {
                throw new InvalidRequestException(sprintf('Invalid size requested'));
            }
        }

        $data = array();

        $cachedInfo = $cache->get(
            Path::combine(
                $workingFolder->getResourceType()->getName(),
                $workingFolder->getClientCurrentFolder(),
                $fileName
            )
        );

        if ($cachedInfo && isset($cachedInfo['width']) && isset($cachedInfo['height'])) {
            $data['originalSize'] = sprintf("%dx%d", $cachedInfo['width'], $cachedInfo['height']);
        }

        $resizedImages = $resizedImageRepository->getResizedImagesList(
            $workingFolder->getResourceType(),
            $workingFolder->getClientCurrentFolder(),
            $fileName,
            $sizes ?: array()
        );

        $data['resized'] = $resizedImages;

        return $data;
    }
}
