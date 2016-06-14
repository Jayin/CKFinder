<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\RenameFolderEvent;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class RenameFolder extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(Permission::FOLDER_RENAME);

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher)
    {
        // The root folder cannot be renamed.
        if ($workingFolder->getClientCurrentFolder() === '/') {
            throw new InvalidRequestException('Cannot rename resource type root folder');
        }

        $newFolderName = (string) $request->query->get('newFolderName');

        $renameFolderEvent = new RenameFolderEvent($this->app, $workingFolder, $newFolderName);

        $dispatcher->dispatch(ZKUploaderEvent::RENAME_FOLDER, $renameFolderEvent);

        if (!$renameFolderEvent->isPropagationStopped()) {
            $newFolderName = $renameFolderEvent->getNewFolderName();

            return $workingFolder->rename($newFolderName);
        }

        return array('renamed' => 0);
    }
}
