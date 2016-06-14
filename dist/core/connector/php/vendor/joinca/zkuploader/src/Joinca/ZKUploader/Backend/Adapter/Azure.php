<?php



namespace Joinca\ZKUploader\Backend\Adapter;

use League\Flysystem\Azure\AzureAdapter as AzureAdapterBase;
use WindowsAzure\Blob\Models\ListBlobsOptions;

class Azure extends AzureAdapterBase implements EmulateRenameDirectoryInterface
{
    /**
     * Emulates changing of directory name.
     *
     * @param string $path
     * @param string $newPath
     *
     * @return bool
     */
    public function renameDirectory($path, $newPath)
    {
        $sourcePath = $this->applyPathPrefix(rtrim($path, '/') . '/');

        $options = new ListBlobsOptions();
        $options->setPrefix($sourcePath);

        /** @var \WindowsAzure\Blob\Models\ListBlobsResult $listResults */
        $listResults = $this->client->listBlobs($this->container, $options);

        foreach ($listResults->getBlobs() as $blob) {
            /** @var \WindowsAzure\Blob\Models\Blob $blob */
            $this->client->copyBlob(
                $this->container,
                $this->replacePath($blob->getName(), $path, $newPath),
                $this->container,
                $blob->getName()
            );
            $this->client->deleteBlob($this->container, $blob->getName());
        }

        return true;
    }

    /**
     * Helper method that replaces a part of the key (path).
     *
     * @param string $objectPath the bucket-relative object path
     * @param string $path       the old backend-relative path
     * @param string $newPath    the new backend-relative path
     *
     * @return string the new bucket-relative path
     */
    protected function replacePath($objectPath, $path, $newPath)
    {
        $objectPath = $this->removePathPrefix($objectPath);
        $newPath = trim($newPath, '/') . '/';
        $path = trim($path, '/') . '/';

        return $this->applyPathPrefix($newPath . substr($objectPath, strlen($path)));
    }
}
