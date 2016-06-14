<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Acl;
use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Error;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\DeleteFileEvent;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Exception\UnauthorizedException;
use Joinca\ZKUploader\Filesystem\File\DeletedFile;
use Joinca\ZKUploader\ResourceType\ResourceTypeFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class DeleteFiles extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(
        Permission::FILE_DELETE
    );

    public function execute(Request $request, ResourceTypeFactory $resourceTypeFactory, Acl $acl, EventDispatcher $dispatcher)
    {
        $deletedFiles = (array) $request->request->get('files');

        $deleted = 0;

        $errors = array();

        // Initial validation
        foreach ($deletedFiles as $arr) {
            if (!isset($arr['name'], $arr['type'], $arr['folder'])) {
                throw new InvalidRequestException('Invalid request');
            }

            if (!$acl->isAllowed($arr['type'], $arr['folder'], Permission::FILE_DELETE)) {
                throw new UnauthorizedException();
            }
        }

        foreach ($deletedFiles as $arr) {
            if (empty($arr['name'])) {
                continue;
            }

            $name   = $arr['name'];
            $type   = $arr['type'];
            $folder = $arr['folder'];

            $resourceType = $resourceTypeFactory->getResourceType($type);

            $deletedFile = new DeletedFile($name, $folder, $resourceType, $this->app);

            if ($deletedFile->isValid()) {
                $deleteFileEvent = new DeleteFileEvent($this->app, $deletedFile);
                $dispatcher->dispatch(ZKUploaderEvent::DELETE_FILE, $deleteFileEvent);

                if (!$deleteFileEvent->isPropagationStopped()) {
                    if ($deletedFile->doDelete()) {
                        $deleted++;
                    }
                }
            }

            $errors = array_merge($errors, $deletedFile->getErrors());
        }

        $data = array('deleted' => $deleted);

        if (!empty($errors)) {
            $data['error'] = array(
                'number' => Error::DELETE_FAILED,
                'errors' => $errors
            );
        }

        return $data;
    }
}
