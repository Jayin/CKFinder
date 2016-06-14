<?php


namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Utils;

class GetFiles extends CommandAbstract
{
    protected $requires = array(Permission::FILE_VIEW);

    public function execute(WorkingFolder $workingFolder)
    {
        $data = new \stdClass();
        $files = $workingFolder->listFiles();

        $data->files = array();

        foreach ($files as $file) {
            $fileObject = array(
                'name' => $file['basename'],
                'date' => Utils::formatDate($file['timestamp']),
                'size' => Utils::formatSize($file['size'])
            );

            $data->files[] = $fileObject;
        }

        // Sort files
        usort($data->files, function ($a, $b) {
            return strnatcasecmp($a['name'], $b['name']);
        });

        return $data;
    }
}
