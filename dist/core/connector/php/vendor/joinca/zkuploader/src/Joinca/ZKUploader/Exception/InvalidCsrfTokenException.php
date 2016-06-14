<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;

/**
 * The "invalid CSRF token" exception class.
 *
 * Thrown when received CSRF tokens do not match.
 *
 * 
 */
class InvalidCsrfTokenException extends ZKUploaderException
{
    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = 'Invalid CSRF token.', $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, Error::INVALID_REQUEST, $parameters, $previous);
    }
}
