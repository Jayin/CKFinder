<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;
use Symfony\Component\HttpFoundation\Response;

/**
 * The "folder not found" exception class.
 *
 * Thrown when the requested folder cannot be found.
 *
 * 
 */
class FolderNotFoundException extends ZKUploaderException
{
    protected $httpStatusCode = Response::HTTP_NOT_FOUND;

    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = 'Folder not found', $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, Error::FOLDER_NOT_FOUND, $parameters, $previous);
    }
}
