<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Acl;
use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Error;
use Joinca\ZKUploader\Event\ZKUploaderEvent;
use Joinca\ZKUploader\Event\CopyFileEvent;
use Joinca\ZKUploader\Exception\InvalidRequestException;
use Joinca\ZKUploader\Exception\UnauthorizedException;
use Joinca\ZKUploader\Filesystem\File\CopiedFile;
use Joinca\ZKUploader\ResourceType\ResourceTypeFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class CopyFiles extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(
        Permission::FILE_RENAME,
        Permission::FILE_CREATE,
        Permission::FILE_DELETE
    );

    public function execute(Request $request, ResourceTypeFactory $resourceTypeFactory, Acl $acl, EventDispatcher $dispatcher)
    {
        $copiedFiles = (array) $request->request->get('files');

        $copied = 0;

        $errors = array();

        // Initial validation
        foreach ($copiedFiles as $arr) {
            if (!isset($arr['name'], $arr['type'], $arr['folder'])) {
                throw new InvalidRequestException();
            }

            if (!$acl->isAllowed($arr['type'], $arr['folder'], Permission::FILE_VIEW)) {
                throw new UnauthorizedException();
            }
        }

        foreach ($copiedFiles as $arr) {
            if (empty($arr['name'])) {
                continue;
            }

            $name   = $arr['name'];
            $type   = $arr['type'];
            $folder = $arr['folder'];

            $resourceType = $resourceTypeFactory->getResourceType($type);

            $copiedFile = new CopiedFile($name, $folder, $resourceType, $this->app);

            $options = isset($arr['options']) ? $arr['options'] : '';

            $copiedFile->setCopyOptions($options);


            if ($copiedFile->isValid()) {
                $copyFileEvent = new CopyFileEvent($this->app, $copiedFile);
                $dispatcher->dispatch(ZKUploaderEvent::COPY_FILE, $copyFileEvent);

                if (!$copyFileEvent->isPropagationStopped()) {
                    if ($copiedFile->doCopy()) {
                        $copied++;
                    }
                }
            }

            $errors = array_merge($errors, $copiedFile->getErrors());
        }

        $data = array('copied' => $copied);

        if (!empty($errors)) {
            $data['error'] = array(
                'number' => Error::COPY_FAILED,
                'errors' => $errors
            );
        }

        return $data;
    }
}
