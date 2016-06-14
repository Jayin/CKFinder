<?php


namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Cache\CacheManager;
use Joinca\ZKUploader\Event\AfterCommandEvent;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\Error;
use Joinca\ZKUploader\Exception\InvalidExtensionException;
use Joinca\ZKUploader\Exception\InvalidNameException;
use Joinca\ZKUploader\Exception\InvalidUploadException;
use Joinca\ZKUploader\Filesystem\File\UploadedFile;
use Joinca\ZKUploader\Event\FileUploadEvent;
use Joinca\ZKUploader\Filesystem\Path;
use Joinca\ZKUploader\Image;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Thumbnail\ThumbnailRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class FileUpload extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(Permission::FILE_CREATE);

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher, Config $config, CacheManager $cache, ThumbnailRepository $thumbsRepository)
    {
        // #111 IE9 download JSON issue workaround
        if ($request->get('asPlainText')) {
            $uploadEvents = array(
                ZKUploaderEvent::AFTER_COMMAND_FILE_UPLOAD,
                ZKUploaderEvent::AFTER_COMMAND_QUICK_UPLOAD
            );

            foreach ($uploadEvents as $eventName) {
                $dispatcher->addListener($eventName, function (AfterCommandEvent $event) {
                    $response = $event->getResponse();
                    $response->headers->set('Content-Type', 'text/plain');
                });
            }
        }

        $uploaded = 0;

        $warningErrorCode = null;
        $upload = $request->files->get('upload');

        if (null === $upload) {
            throw new InvalidUploadException();
        }

        $uploadedFile = new UploadedFile($upload, $this->app);

        if (!$uploadedFile->isValid()) {
            throw new InvalidUploadException($uploadedFile->getErrorMessage());
        }

        $uploadedFile->sanitizeFilename();

        if ($uploadedFile->wasRenamed()) {
            $warningErrorCode = Error::UPLOADED_INVALID_NAME_RENAMED;
        }

        if (!$uploadedFile->hasValidFilename() || $uploadedFile->isHiddenFile()) {
            throw new InvalidNameException();
        }

        if (!$uploadedFile->hasAllowedExtension()) {
            throw new InvalidExtensionException();
        }

        // Autorename if required
        $overwriteOnUpload = $config->get('overwriteOnUpload');
        if (!$overwriteOnUpload && $uploadedFile->autorename()) {
            $warningErrorCode = Error::UPLOADED_FILE_RENAMED;
        }

        $fileName = $uploadedFile->getFilename();

        if (!$uploadedFile->isAllowedHtmlFile() && $uploadedFile->containsHtml()) {
            throw new InvalidUploadException('HTML detected in disallowed file type', Error::UPLOADED_WRONG_HTML_FILE);
        }

        if ($config->get('secureImageUploads') && $uploadedFile->isImage() && !$uploadedFile->isValidImage()) {
            throw new InvalidUploadException('Invalid upload: corrupted image', Error::UPLOADED_CORRUPT);
        }

        $maxFileSize = $workingFolder->getResourceType()->getMaxSize();

        if (!$config->get('checkSizeAfterScaling') && $maxFileSize && $uploadedFile->getSize() > $maxFileSize) {
            throw new InvalidUploadException('Uploaded file is too big', Error::UPLOADED_TOO_BIG);
        }

        if (Image::isSupportedExtension($uploadedFile->getExtension())) {
            $imagesConfig = $config->get('images');
            $image = Image::create($uploadedFile->getContents());

            if ($imagesConfig['maxWidth'] && $image->getWidth() > $imagesConfig['maxWidth'] ||
                $imagesConfig['maxHeight'] && $image->getHeight() > $imagesConfig['maxHeight']) {
                $image->resize($imagesConfig['maxWidth'], $imagesConfig['maxHeight'], $imagesConfig['quality']);
                $imageData = $image->getData();
                $uploadedFile->save($imageData);
            }

            $cache->set(
                Path::combine(
                    $workingFolder->getResourceType()->getName(),
                    $workingFolder->getClientCurrentFolder(),
                    $fileName),
                $image->getInfo()
            );

            unset($imageData);
            unset($image);
        }

        if ($maxFileSize && $uploadedFile->getSize() > $maxFileSize) {
            throw new InvalidUploadException('Uploaded file is too big', Error::UPLOADED_TOO_BIG);
        }

        $event = new FileUploadEvent($this->app, $uploadedFile);
        $dispatcher->dispatch(ZKUploaderEvent::FILE_UPLOAD, $event);

        if (!$event->isPropagationStopped()) {
            $uploadedFileStream = $uploadedFile->getContentsStream();
            $uploaded = (int) $workingFolder->putStream($fileName, $uploadedFileStream, $uploadedFile->getMimeType());

            if ($overwriteOnUpload) {
                $thumbsRepository->deleteThumbnails(
                    $workingFolder->getResourceType(),
                    $workingFolder->getClientCurrentFolder(),
                    $fileName
                );
            }

            if (!$uploaded) {
                $warningErrorCode = Error::ACCESS_DENIED;
            }
        }

        $responseData = array(
            'fileName' => $fileName,
            'uploaded' => $uploaded
        );

        if ($warningErrorCode) {
            $errorMessage = $this->app['translator']->translateErrorMessage($warningErrorCode, array('name' => $fileName));
            $responseData['error'] = array(
                'number'  => $warningErrorCode,
                'message' => $errorMessage
            );
        }

        return $responseData;
    }
}
