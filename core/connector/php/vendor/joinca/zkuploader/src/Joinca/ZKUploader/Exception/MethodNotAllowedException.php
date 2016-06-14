<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;
use Symfony\Component\HttpFoundation\Response;

/**
 * The "invalid HTTP method" exception class.
 *
 * Thrown when a command is called using the invalid HTTP method.
 *
 * 
 */
class MethodNotAllowedException extends ZKUploaderException
{
    protected $httpStatusCode = Response::HTTP_METHOD_NOT_ALLOWED;

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
