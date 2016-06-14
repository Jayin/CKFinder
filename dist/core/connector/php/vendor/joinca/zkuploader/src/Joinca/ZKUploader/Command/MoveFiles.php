<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Acl;
use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Error;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\MoveFileEvent;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Exception\UnauthorizedException;
use Joinca\ZKUploader\Filesystem\File\MovedFile;
use Joinca\ZKUploader\ResourceType\ResourceTypeFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class MoveFiles extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(
        Permission::FILE_RENAME,
        Permission::FILE_CREATE,
        Permission::FILE_DELETE
    );

    public function execute(Request $request, ResourceTypeFactory $resourceTypeFactory, Acl $acl, EventDispatcher $dispatcher)
    {
        $movedFiles = (array) $request->request->get('files');

        $moved = 0;

        $errors = array();

        // Initial validation
        foreach ($movedFiles as $arr) {
            if (!isset($arr['name'], $arr['type'], $arr['folder'])) {
                throw new InvalidRequestException('Invalid request');
            }

            if (!$acl->isAllowed($arr['type'], $arr['folder'], Permission::FILE_VIEW | Permission::FILE_DELETE)) {
                throw new UnauthorizedException('Unauthorized');
            }
        }

        foreach ($movedFiles as $arr) {
            if (empty($arr['name'])) {
                continue;
            }

            $name   = $arr['name'];
            $type   = $arr['type'];
            $folder = $arr['folder'];

            $resourceType = $resourceTypeFactory->getResourceType($type);

            $movedFile = new MovedFile($name, $folder, $resourceType, $this->app);

            $options = isset($arr['options']) ? $arr['options'] : '';

            $movedFile->setCopyOptions($options);


            if ($movedFile->isValid()) {
                $moveFileEvent = new MoveFileEvent($this->app, $movedFile);
                $dispatcher->dispatch(ZKUploaderEvent::MOVE_FILE, $moveFileEvent);

                if (!$moveFileEvent->isPropagationStopped()) {
                    if ($movedFile->doMove()) {
                        $moved++;
                    }
                }
            }

            $errors = array_merge($errors, $movedFile->getErrors());
        }

        $data = array('moved' => $moved);

        if (!empty($errors)) {
            $data['error'] = array(
                'number' => Error::MOVE_FAILED,
                'errors' => $errors
            );
        }

        return $data;
    }
}
