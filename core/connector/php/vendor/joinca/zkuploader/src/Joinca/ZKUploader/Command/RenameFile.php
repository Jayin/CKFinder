<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\RenameFileEvent;
use Joinca\ZKUploader\Exception\AccessDeniedException;
use Joinca\ZKUploader\Exception\InvalidNameException;
use Joinca\ZKUploader\Filesystem\File\RenamedFile;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class RenameFile extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(Permission::FILE_RENAME);

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher)
    {
        $fileName = (string) $request->query->get('fileName');
        $newFileName = (string) $request->query->get('newFileName');

        if (null === $fileName || null === $newFileName) {
            throw new InvalidNameException('Invalid file name');
        }

        $renamedFile = new RenamedFile(
            $newFileName,
            $fileName,
            $workingFolder->getClientCurrentFolder(),
            $workingFolder->getResourceType(),
            $this->app
        );

        $renamed = false;

        if ($renamedFile->isValid()) {
            $renamedFileEvent = new RenameFileEvent($this->app, $renamedFile);

            $dispatcher->dispatch(ZKUploaderEvent::RENAME_FILE, $renamedFileEvent);

            if (!$renamedFileEvent->isPropagationStopped()) {
                $renamed = $renamedFile->doRename();
            }
        }

        return array(
            'name'    => $fileName,
            'newName' => $renamedFile->getNewFileName(),
            'renamed' => (int) $renamed
        );
    }
}
