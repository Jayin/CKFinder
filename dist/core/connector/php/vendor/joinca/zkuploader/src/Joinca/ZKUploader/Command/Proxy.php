<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\DownloadFileEvent;
use Joinca\ZKUploader\Event\ProxyDownloadEvent;
use Joinca\ZKUploader\Exception\AccessDeniedException;
use Joinca\ZKUploader\Exception\FileNotFoundException;
use Joinca\ZKUploader\Exception\InvalidExtensionException;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Filesystem\File\DownloadedFile;
use Joinca\ZKUploader\Filesystem\File\File;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Utils;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Proxy extends CommandAbstract
{
    protected $requires = array(Permission::FILE_VIEW);

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher, Config $config)
    {
        $fileName = (string) $request->query->get('fileName');
        $thumbnailFileName = (string) $request->query->get('thumbnail');

        if (!File::isValidName($fileName, $config->get('disallowUnsafeCharacters'))) {
            throw new InvalidRequestException(sprintf('Invalid file name: %s', $fileName));
        }

        $cacheLifetime = (int) $request->query->get('cache');

        if (!$workingFolder->containsFile($fileName)) {
            throw new FileNotFoundException();
        }

        if ($thumbnailFileName) {
            if (!File::isValidName($thumbnailFileName, $config->get('disallowUnsafeCharacters'))) {
                throw new InvalidRequestException(sprintf('Invalid resized image file name: %s', $fileName));
            }

            if (!$workingFolder->getResourceType()->isAllowedExtension(pathinfo($thumbnailFileName, PATHINFO_EXTENSION))) {
                throw new InvalidExtensionException();
            }

            $resizedImageRespository = $this->app->getResizedImageRepository();
            $file = $resizedImageRespository->getExistingResizedImage(
                $workingFolder->getResourceType(),
                $workingFolder->getClientCurrentFolder(),
                $fileName,
                $thumbnailFileName
            );
            $dataStream = $file->readStream();
        } else {
            $file = new DownloadedFile($fileName, $this->app);
            $file->isValid();
            $dataStream = $workingFolder->readStream($file->getFilename());
        }

        $proxyDownload = new ProxyDownloadEvent($this->app, $file);

        $dispatcher->dispatch(ZKUploaderEvent::PROXY_DOWNLOAD, $proxyDownload);

        if ($proxyDownload->isPropagationStopped()) {
            throw new AccessDeniedException();
        }

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $file->getMimeType());
        $response->headers->set('Content-Length', $file->getSize());
        $response->headers->set('Content-Disposition', 'inline; filename="' . $fileName. '"');

        if ($cacheLifetime > 0) {
            Utils::removeSessionCacheHeaders();

            $response->setPublic();
            $response->setEtag(dechex($file->getTimestamp()) . "-" . dechex($file->getSize()));

            $lastModificationDate = new \DateTime();
            $lastModificationDate->setTimestamp($file->getTimestamp());

            $response->setLastModified($lastModificationDate);

            if ($response->isNotModified($request)) {
                return $response;
            }

            $response->setMaxAge($cacheLifetime);

            $expireTime = new \DateTime();
            $expireTime->modify('+' . $cacheLifetime . 'seconds');
            $response->setExpires($expireTime);
        }

        $chunkSize = 1024 * 100;

        $response->setCallback(function () use ($dataStream, $chunkSize) {
            if ($dataStream === false) {
                return false;
            }
            while (!feof($dataStream)) {
                echo fread($dataStream, $chunkSize);
                flush();
                @set_time_limit(8);
            }

            return true;
        });

        return $response;
    }
}
