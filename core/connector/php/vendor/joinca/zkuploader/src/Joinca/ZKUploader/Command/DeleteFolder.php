<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\DeleteFolderEvent;
use Joinca\ZKUploader\Exception\AccessDeniedException;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class DeleteFolder extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(Permission::FOLDER_DELETE);

    public function execute(WorkingFolder $workingFolder, EventDispatcher $dispatcher)
    {
        // The root folder cannot be deleted.
        if ($workingFolder->getClientCurrentFolder() === '/') {
            throw new InvalidRequestException('Cannot delete resource type root folder');
        }

        $deleteFolderEvent = new DeleteFolderEvent($this->app, $workingFolder);

        $dispatcher->dispatch(ZKUploaderEvent::DELETE_FOLDER, $deleteFolderEvent);

        $deleted = false;

        if (!$deleteFolderEvent->isPropagationStopped()) {
            $deleted = $workingFolder->delete();
        }

        if (!$deleted) {
            throw new AccessDeniedException();
        }

        return array('deleted' => (int) $deleted);
    }
}
