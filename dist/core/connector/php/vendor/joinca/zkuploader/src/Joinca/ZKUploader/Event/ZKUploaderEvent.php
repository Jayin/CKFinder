<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * The ZKUploaderEvent class.
 *
 * The base class for all ZKUploader events.
 */
class ZKUploaderEvent extends Event
{
    /**
     * The beforeCommand events.
     *
     * These events occur before a command is executed, after a particular
     * command is resolved, i.e. it is decided which command class should be used
     * to handle the current request.
     */
    const BEFORE_COMMAND_PREFIX             = 'zkuploader.beforeCommand.';

    const BEFORE_COMMAND_INIT               = 'zkuploader.beforeCommand.init';
    const BEFORE_COMMAND_COPY_FILES         = 'zkuploader.beforeCommand.copyFiles';
    const BEFORE_COMMAND_CREATE_FOLDER      = 'zkuploader.beforeCommand.createFolder';
    const BEFORE_COMMAND_DELETE_FILES       = 'zkuploader.beforeCommand.deleteFiles';
    const BEFORE_COMMAND_DELETE_FOLDER      = 'zkuploader.beforeCommand.deleteFolder';
    const BEFORE_COMMAND_DOWNLOAD_FILE      = 'zkuploader.beforeCommand.downloadFile';
    const BEFORE_COMMAND_FILE_UPLOAD        = 'zkuploader.beforeCommand.fileUpload';
    const BEFORE_COMMAND_GET_FILES          = 'zkuploader.beforeCommand.getFiles';
    const BEFORE_COMMAND_GET_FILE_URL       = 'zkuploader.beforeCommand.getFileUrl';
    const BEFORE_COMMAND_GET_FOLDERS        = 'zkuploader.beforeCommand.getFolders';
    const BEFORE_COMMAND_GET_RESIZED_IMAGES = 'zkuploader.beforeCommand.getResizedImages';
    const BEFORE_COMMAND_IMAGE_EDIT         = 'zkuploader.beforeCommand.imageEdit';
    const BEFORE_COMMAND_IMAGE_INFO         = 'zkuploader.beforeCommand.imageInfo';
    const BEFORE_COMMAND_IMAGE_PREVIEW      = 'zkuploader.beforeCommand.imagePreview';
    const BEFORE_COMMAND_IMAGE_RESIZE       = 'zkuploader.beforeCommand.imageResize';
    const BEFORE_COMMAND_MOVE_FILES         = 'zkuploader.beforeCommand.moveFiles';
    const BEFORE_COMMAND_QUICK_UPLOAD       = 'zkuploader.beforeCommand.quickUpload';
    const BEFORE_COMMAND_RENAME_FILE        = 'zkuploader.beforeCommand.renameFile';
    const BEFORE_COMMAND_RENAME_FOLDER      = 'zkuploader.beforeCommand.renameFolder';
    const BEFORE_COMMAND_SAVE_IMAGE         = 'zkuploader.beforeCommand.saveImage';
    const BEFORE_COMMAND_THUMBNAIL          = 'zkuploader.beforeCommand.thumbnail';

    /**
     * Intermediate events.
     */
    const COPY_FILE              = 'zkuploader.copyFiles.copy';
    const CREATE_FOLDER          = 'zkuploader.createFolder.create';
    const DELETE_FILE            = 'zkuploader.deleteFiles.delete';
    const DELETE_FOLDER          = 'zkuploader.deleteFolder.delete';
    const DOWNLOAD_FILE          = 'zkuploader.downloadFile.download';
    const PROXY_DOWNLOAD         = 'zkuploader.proxy.download';
    const FILE_UPLOAD            = 'zkuploader.uploadFile.upload';
    const MOVE_FILE              = 'zkuploader.moveFiles.move';
    const RENAME_FILE            = 'zkuploader.renameFile.rename';
    const RENAME_FOLDER          = 'zkuploader.renameFolder.rename';
    const SAVE_IMAGE             = 'zkuploader.saveImage.save';
    const EDIT_IMAGE             = 'zkuploader.imageEdit.save';
    const CREATE_THUMBNAIL       = 'zkuploader.thumbnail.createThumbnail';
    const CREATE_RESIZED_IMAGE   = 'zkuploader.imageResize.createResizedImage';

    const CREATE_RESPONSE_PREFIX = 'zkuploader.createResponse.';

    /**
     * The afterCommand events.
     *
     * These events occur after a command execution, when a response for
     * a command was created.
     */
    const AFTER_COMMAND_PREFIX             = 'zkuploader.afterCommand.';

    const AFTER_COMMAND_INIT               = 'zkuploader.afterCommand.init';
    const AFTER_COMMAND_COPY_FILES         = 'zkuploader.afterCommand.copyFiles';
    const AFTER_COMMAND_CREATE_FOLDER      = 'zkuploader.afterCommand.createFolder';
    const AFTER_COMMAND_DELETE_FILES       = 'zkuploader.afterCommand.deleteFiles';
    const AFTER_COMMAND_DELETE_FOLDER      = 'zkuploader.afterCommand.deleteFolder';
    const AFTER_COMMAND_DOWNLOAD_FILE      = 'zkuploader.afterCommand.downloadFile';
    const AFTER_COMMAND_FILE_UPLOAD        = 'zkuploader.afterCommand.fileUpload';
    const AFTER_COMMAND_GET_FILES          = 'zkuploader.afterCommand.getFiles';
    const AFTER_COMMAND_GET_FILE_URL       = 'zkuploader.afterCommand.getFileUrl';
    const AFTER_COMMAND_GET_FOLDERS        = 'zkuploader.afterCommand.getFolders';
    const AFTER_COMMAND_GET_RESIZED_IMAGES = 'zkuploader.afterCommand.getResizedImages';
    const AFTER_COMMAND_IMAGE_EDIT         = 'zkuploader.afterCommand.imageEdit';
    const AFTER_COMMAND_IMAGE_INFO         = 'zkuploader.afterCommand.imageInfo';
    const AFTER_COMMAND_IMAGE_PREVIEW      = 'zkuploader.afterCommand.imagePreview';
    const AFTER_COMMAND_IMAGE_RESIZE       = 'zkuploader.afterCommand.imageResize';
    const AFTER_COMMAND_MOVE_FILES         = 'zkuploader.afterCommand.moveFiles';
    const AFTER_COMMAND_QUICK_UPLOAD       = 'zkuploader.afterCommand.quickUpload';
    const AFTER_COMMAND_RENAME_FILE        = 'zkuploader.afterCommand.renameFile';
    const AFTER_COMMAND_RENAME_FOLDER      = 'zkuploader.afterCommand.renameFolder';
    const AFTER_COMMAND_SAVE_IMAGE         = 'zkuploader.afterCommand.saveImage';
    const AFTER_COMMAND_THUMBNAIL          = 'zkuploader.afterCommand.thumbnail';

    /**
     * The ZKUploader instance.
     *
     * @var ZKUploader $app
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param ZKUploader $app
     */
    public function __construct(ZKUploader $app)
    {
        $this->app = $app;
    }

    /**
     * Returns the application dependency injection container.
     *
     * @return ZKUploader
     */
    public function getContainer()
    {
        return $this->app;
    }

    /**
     * Returns the current request object.
     *
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->app['request_stack']->getCurrentRequest();
    }
}
