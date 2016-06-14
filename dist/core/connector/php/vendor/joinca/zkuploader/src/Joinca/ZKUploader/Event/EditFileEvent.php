<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\File\EditedFile;

/**
 * The EditFileEvent event class.
 */
class EditFileEvent extends ZKUploaderEvent
{
    /**
     * @var EditedFile $uploadedFile
     */
    protected $editedFile;

    /**
     * @var string $newContents
     */
    protected $newContents;

    /**
     * Constructor.
     *
     * @param ZKUploader   $app
     * @param EditedFile $editedFile
     */
    public function __construct(ZKUploader $app, EditedFile $editedFile)
    {
        $this->editedFile = $editedFile;

        parent::__construct($app);
    }

    /**
     * Returns the edited file object.
     *
     * @return EditedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getEditedFile()
    {
        return $this->editedFile;
    }

    /**
     * Returns the edited file object.
     *
     * @return EditedFile
     */
    public function getFile()
    {
        return $this->editedFile;
    }

    /**
     * Returns new contents of the edited file.
     *
     * @return string
     */
    public function getNewContents()
    {
        return $this->editedFile->getNewContents();
    }

    /**
     * Sets new contents for the edited file.
     *
     * @param string $newContents
     */
    public function setNewContents($newContents)
    {
        $this->editedFile->setNewContents($newContents);
    }
}
