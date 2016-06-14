<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Acl\Permission;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Filesystem\Path;

class GetFolders extends CommandAbstract
{
    protected $requires = array(Permission::FOLDER_VIEW);

    public function execute(WorkingFolder $workingFolder)
    {
        $directories = $workingFolder->listDirectories();

        $data = new \stdClass();
        $data->folders = array();

        $backend = $workingFolder->getBackend();

        $resourceType = $workingFolder->getResourceType();

        foreach ($directories as $directory) {
            $data->folders[] = array(
                'name'        => $directory['basename'],
                'hasChildren' => $backend->containsDirectories($resourceType, Path::combine($workingFolder->getClientCurrentFolder(), $directory['basename'])),
                'acl'         => $directory['acl']
            );
        }

        // Sort folders
        usort($data->folders, function ($a, $b) {
            return strnatcasecmp($a['name'], $b['name']);
        });

        return $data;
    }
}
