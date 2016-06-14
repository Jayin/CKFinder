<?php



namespace Joinca\ZKUploader\Filesystem\File;

use Joinca\ZKUploader\Exception\InvalidUploadException;

/**
 * The EditedImage class.
 *
 * Represents an image file that is edited.
 */
class EditedImage extends EditedFile
{
    /**
     * @var int
     */
    protected $newWidth;

    /**
     * @var int
     */
    protected $newHeight;

    /**
     * Sets new image dimensions.
     *
     * @param int $newWidth
     * @param int $newHeight
     */
    public function setNewDimensions($newWidth, $newHeight)
    {
        $this->newWidth = $newWidth;
        $this->newHeight = $newHeight;
    }

    /**
     * @copydoc EditedFile::isValid()
     */
    public function isValid()
    {
        $imagesConfig = $this->config->get('images');

        if ($imagesConfig['maxWidth'] && $this->newWidth > $imagesConfig['maxWidth'] ||
            $imagesConfig['maxHeight'] && $this->newHeight > $imagesConfig['maxHeight']) {
            throw new InvalidUploadException('The image dimensions exceeds images.maxWidth or images.maxHeight');
        }

        return parent::isValid();
    }
}
