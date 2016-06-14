<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\DownloadFileEvent;
use Joinca\ZKUploader\Exception\AccessDeniedException;
use Joinca\ZKUploader\Filesystem\File\DownloadedFile;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadFile extends CommandAbstract
{
    protected $requires = array(Permission::FILE_VIEW);

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher)
    {
        $fileName = (string) $request->query->get('fileName');

        $downloadedFile = new DownloadedFile($fileName, $this->app);

        $downloadedFile->isValid();

        $downloadedFileEvent = new DownloadFileEvent($this->app, $downloadedFile);

        $dispatcher->dispatch(ZKUploaderEvent::DOWNLOAD_FILE, $downloadedFileEvent);

        if ($downloadedFileEvent->isPropagationStopped()) {
            throw new AccessDeniedException();
        }

        $response = new StreamedResponse();

        $response->headers->set('Cache-Control', 'cache, must-revalidate');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Expires', '0');

        if ($request->get('format') === 'text') {
            $response->headers->set('Content-Type', 'text/plain; charset=utf-8');
        } else {
            $userAgent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $encodedName = str_replace("\"", "\\\"", $fileName);
            if (strpos($userAgent, 'MSIE') !== false) {
                $encodedName = str_replace(array("+", "%2E"), array(" ", "."), urlencode($encodedName));
            }
            $response->headers->set('Content-Type', 'application/octet-stream; name="' . $fileName . '"');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $encodedName. '"');
        }

        $response->headers->set('Content-Length', $downloadedFile->getSize());

        $fileStream = $workingFolder->readStream($downloadedFile->getFilename());
        $chunkSize = 1024 * 100; // how many bytes per chunk

        $response->setCallback(function () use ($fileStream, $chunkSize) {
            if ($fileStream === false) {
                return false;
            }
            while (!feof($fileStream)) {
                echo fread($fileStream, $chunkSize);
                flush();
                @set_time_limit(8);
            }

            return true;
        });

        return $response;
    }
}
