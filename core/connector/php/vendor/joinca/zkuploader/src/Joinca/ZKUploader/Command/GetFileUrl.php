<?php


namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Symfony\Component\HttpFoundation\Request;

class GetFileUrl extends CommandAbstract
{
    protected $requires = array(Permission::FILE_VIEW);

    public function execute(WorkingFolder $workingFolder, Request $request)
    {
        $fileName = (string) $request->get('fileName');
        $thumbnail = (string) $request->get('thumbnail');

        $fileNames = (array) $request->get('fileNames');

        if (!empty($fileNames)) {
            $urls = array();

            foreach ($fileNames as $fileName) {
                $urls[$fileName] = $workingFolder->getFileUrl($fileName);
            }

            return array('urls' => $urls);
        }

        return array(
            'url' => $workingFolder->getFileUrl($fileName, $thumbnail)
        );
    }
}
