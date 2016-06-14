<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;

/**
 * The "invalid extension" exception class.
 *
 * Thrown when a file has an invalid extension, for example if the extension
 * is not allowed for the resource type.
 *
 * 
 */
class InvalidExtensionException extends ZKUploaderException
{
    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = 'Invalid extension', $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, Error::INVALID_EXTENSION, $parameters, $previous);
    }
}
