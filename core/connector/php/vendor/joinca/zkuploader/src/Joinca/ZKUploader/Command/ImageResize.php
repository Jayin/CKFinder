<?php


namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Exception\FileNotFoundException;
use Joinca\ZKUploader\Exception\InvalidNameException;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Filesystem\File\File;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Image;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\ResizedImage\ResizedImageRepository;
use Symfony\Component\HttpFoundation\Request;

class ImageResize extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(Permission::FILE_VIEW, Permission::IMAGE_RESIZE);

    public function execute(Request $request, WorkingFolder $workingFolder, Config $config, ResizedImageRepository $resizedImageRepository)
    {
        $fileName = (string) $request->query->get('fileName');

        if (null === $fileName || !File::isValidName($fileName, $config->get('disallowUnsafeCharacters'))) {
            throw new InvalidRequestException('Invalid file name');
        }

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!Image::isSupportedExtension($ext)) {
            throw new InvalidNameException('Invalid source file name');
        }

        if (!$workingFolder->containsFile($fileName)) {
            throw new FileNotFoundException();
        }

        list($requestedWidth, $requestedHeight) = Image::parseSize((string) $request->query->get('size'));

        $resizedImage = $resizedImageRepository->getResizedImage(
            $workingFolder->getResourceType(),
            $workingFolder->getClientCurrentFolder(),
            $fileName,
            $requestedWidth,
            $requestedHeight
        );

        return array('url' => $resizedImage->getUrl());
    }
}
