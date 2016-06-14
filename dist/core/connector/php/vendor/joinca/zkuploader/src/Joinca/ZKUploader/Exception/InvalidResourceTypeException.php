<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;

/**
 * The "invalid resource type" exception class.
 *
 * Thrown when a request is trying to access an invalid resource type (e.g. one that does not exist).
 *
 * 
 */
class InvalidResourceTypeException extends ZKUploaderException
{
    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = null, $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, Error::INVALID_TYPE, $parameters, $previous);
    }
}
