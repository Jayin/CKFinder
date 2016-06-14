<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;

/**
 * The "invalid command" exception class.
 *
 * Thrown when the command passed in the `command` URL parameter is invalid.
 *
 * 
 */
class InvalidCommandException extends ZKUploaderException
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
        parent::__construct($message, Error::INVALID_COMMAND, $parameters, $previous);
    }
}
