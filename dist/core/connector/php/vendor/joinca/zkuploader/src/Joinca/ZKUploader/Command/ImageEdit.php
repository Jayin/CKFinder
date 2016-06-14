<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Acl;
use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\EditFileEvent;
use Joinca\ZKUploader\Exception\InvalidExtensionException;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Exception\InvalidUploadException;
use Joinca\ZKUploader\Exception\UnauthorizedException;
use Joinca\ZKUploader\Filesystem\File\EditedImage;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Image;
use Joinca\ZKUploader\ResizedImage\ResizedImageRepository;
use Joinca\ZKUploader\Thumbnail\ThumbnailRepository;
use Joinca\ZKUploader\Utils;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * The ImageEdit command class.
 *
 * This command performs basic image modifications:
 * - crop
 * - rotate
 * - resize
 *
 */
class ImageEdit extends CommandAbstract
{
    const OPERATION_CROP   = 'crop';
    const OPERATION_ROTATE = 'rotate';
    const OPERATION_RESIZE = 'resize';

    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(Permission::FILE_CREATE);

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher, Acl $acl, ResizedImageRepository $resizedImageRepository, ThumbnailRepository $thumbnailRepository, Config $config)
    {
        $fileName = (string) $request->get('fileName');
        $newFileName = (string) $request->get('newFileName');

        $editedImage = new EditedImage($fileName, $this->app, $newFileName);

        $resourceType = $workingFolder->getResourceType();

        if (null === $newFileName) {
            $resourceTypeName = $resourceType->getName();
            $path = $workingFolder->getClientCurrentFolder();

            if (!$acl->isAllowed($resourceTypeName, $path, Permission::FILE_DELETE)) {
                throw new UnauthorizedException(sprintf('Unauthorized: no FILE_DELETE permission in %s:%s', $resourceTypeName, $path));
            }
        }

        if (!Image::isSupportedExtension($editedImage->getExtension())) {
            throw new InvalidExtensionException('Unsupported image type or not image file');
        }

        $image = Image::create($editedImage->getContents());

        $actions = (array) $request->get('actions');

        if (empty($actions)) {
            throw new InvalidRequestException();
        }

        foreach ($actions as $actionInfo) {
            if (!isset($actionInfo['action'])) {
                throw new InvalidRequestException('ImageEdit: action name missing');
            }

            switch ($actionInfo['action']) {
                case self::OPERATION_CROP:
                    if (!Utils::arrayContainsKeys($actionInfo, array('x', 'y', 'width', 'height'))) {
                        throw new InvalidRequestException();
                    }
                    $x = $actionInfo['x'];
                    $y = $actionInfo['y'];
                    $width = $actionInfo['width'];
                    $height = $actionInfo['height'];
                    $image->crop($x, $y, $width, $height);
                    break;

                case self::OPERATION_ROTATE:
                    if (!isset($actionInfo['angle'])) {
                        throw new InvalidRequestException();
                    }
                    $degrees = $actionInfo['angle'];
                    $bgcolor = isset($actionInfo['bgcolor']) ? $actionInfo['bgcolor'] : 0;
                    $image->rotate($degrees, $bgcolor);
                    break;

                case self::OPERATION_RESIZE:
                    if (!Utils::arrayContainsKeys($actionInfo, array('width', 'height'))) {
                        throw new InvalidRequestException();
                    }

                    $imagesConfig = $config->get('images');

                    $width = $imagesConfig['maxWidth'] && $actionInfo['width'] > $imagesConfig['maxWidth'] ? $imagesConfig['maxWidth'] : $actionInfo['width'];
                    $height = $imagesConfig['maxHeight'] && $actionInfo['height'] > $imagesConfig['maxHeight'] ? $imagesConfig['maxHeight'] : $actionInfo['height'];
                    $image->resize((int) $width, (int) $height, $imagesConfig['quality']);
                    break;
            }
        }

        $editFileEvent = new EditFileEvent($this->app, $editedImage);

        $editedImage->setNewContents($image->getData());
        $editedImage->setNewDimensions($image->getWidth(), $image->getHeight());

        if (!$editedImage->isValid()) {
            throw new InvalidUploadException('Invalid file provided');
        }

        $dispatcher->dispatch(ZKUploaderEvent::EDIT_IMAGE, $editFileEvent);

        $saved = false;

        if (!$editFileEvent->isPropagationStopped()) {
            $saved = $editedImage->save($editFileEvent->getNewContents());

            //Remove thumbnails and resized images in case if file is overwritten
            if ($newFileName === null && $saved) {
                $thumbnailRepository->deleteThumbnails($resourceType, $workingFolder->getClientCurrentFolder(), $fileName);
                $resizedImageRepository->deleteResizedImages($resourceType, $workingFolder->getClientCurrentFolder(), $fileName);
            }
        }

        return array(
            'saved' => (int) $saved,
            'date'  => Utils::formatDate(time())
        );
    }
}
