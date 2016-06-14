<?php


namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\CreateFolderEvent;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class CreateFolder extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(Permission::FOLDER_CREATE);

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher)
    {
        $newFolderName = (string) $request->query->get('newFolderName', '');

        $createFolderEvent = new CreateFolderEvent($this->app, $workingFolder, $newFolderName);

        $dispatcher->dispatch(ZKUploaderEvent::CREATE_FOLDER, $createFolderEvent);

        $created = false;

        if (!$createFolderEvent->isPropagationStopped()) {
            $newFolderName = $createFolderEvent->getNewFolderName();
            $created = $workingFolder->createDir($newFolderName);
        }

        return array('newFolder' => $newFolderName, 'created' => (int) $created);
    }
}
