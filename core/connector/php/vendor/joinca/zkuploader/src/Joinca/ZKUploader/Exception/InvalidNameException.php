<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;

/**
 * The "invalid name" exception class.
 *
 * Thrown when a file or folder has an invalid name, e.g. contains
 * disallowed characters like "/" or "\".
 *
 * 
 */
class InvalidNameException extends ZKUploaderException
{
    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = 'Invalid name', $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, Error::INVALID_NAME, $parameters, $previous);
    }
}
