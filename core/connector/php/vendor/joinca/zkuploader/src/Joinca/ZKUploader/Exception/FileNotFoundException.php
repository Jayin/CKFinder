<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;
use Symfony\Component\HttpFoundation\Response;

/**
 * The "file not found" exception class.
 *
 * Thrown when the requested file cannot be found.
 *
 * 
 */
class FileNotFoundException extends ZKUploaderException
{
    protected $httpStatusCode = Response::HTTP_NOT_FOUND;

    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = 'File not found', $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, Error::FILE_NOT_FOUND, $parameters, $previous);
    }
}
