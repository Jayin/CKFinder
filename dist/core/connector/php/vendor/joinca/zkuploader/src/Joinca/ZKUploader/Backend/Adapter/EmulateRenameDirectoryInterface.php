<?php


namespace Joinca\ZKUploader\Backend\Adapter;

/**
 * The RenameDirectoryInterface interface.
 *
 * An interface implemented by adapters that do not support renaming folders, and emulate this operation.
 */
interface EmulateRenameDirectoryInterface
{
    /**
     * Changes the directory name.
     *
     * @param string $path
     * @param string $newPath
     *
     * @return bool
     */
    public function renameDirectory($path, $newPath);
}
