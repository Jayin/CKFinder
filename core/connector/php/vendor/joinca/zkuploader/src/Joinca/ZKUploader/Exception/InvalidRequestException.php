<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;

/**
 * The "invalid request" exception class.
 *
 * Thrown when an invalid command request was received.
 *
 * 
 */
class InvalidRequestException extends ZKUploaderException
{
    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = 'Invalid request', $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, Error::INVALID_REQUEST, $parameters, $previous);
    }
}
