<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Cache\CacheManager;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\Exception\FileNotFoundException;
use Joinca\ZKUploader\Exception\InvalidNameException;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Filesystem\File\DownloadedFile;
use Joinca\ZKUploader\Filesystem\File\File;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Filesystem\Path;
use Joinca\ZKUploader\Image;
use Symfony\Component\HttpFoundation\Request;

class ImageInfo extends CommandAbstract
{
    protected $requires = array(
        Permission::FILE_VIEW
    );

    public function execute(Request $request, WorkingFolder $workingFolder, Config $config, CacheManager $cache)
    {
        $fileName = (string) $request->get('fileName');

        if (null === $fileName || !File::isValidName($fileName, $config->get('disallowUnsafeCharacters'))) {
            throw new InvalidRequestException('Invalid file name');
        }

        if (!Image::isSupportedExtension(pathinfo($fileName, PATHINFO_EXTENSION))) {
            throw new InvalidNameException('Invalid source file name');
        }

        if (!$workingFolder->containsFile($fileName)) {
            throw new FileNotFoundException();
        }

        $cachePath = Path::combine(
            $workingFolder->getResourceType()->getName(),
            $workingFolder->getClientCurrentFolder(),
            $fileName
        );

        $imageInfo = array();

        $cachedInfo = $cache->get($cachePath);

        if ($cachedInfo && isset($cachedInfo['width']) && isset($cachedInfo['height'])) {
            $imageInfo = $cachedInfo;
        } else {
            $file = new DownloadedFile($fileName, $this->app);

            if ($file->isValid()) {
                $image = Image::create($file->getContents());
                $imageInfo = $image->getInfo();
                $cache->set($cachePath, $imageInfo);
            }
        }

        return $imageInfo;
    }
}
