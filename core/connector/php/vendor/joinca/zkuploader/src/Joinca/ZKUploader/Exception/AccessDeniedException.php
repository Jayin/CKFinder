<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;
use Symfony\Component\HttpFoundation\Response;

/**
 * The "access denied" exception.
 *
 * Thrown when file system permissions do not allow to perform an operation
 * such as accessing a directory or writing a file.
 *
 * 
 */
class AccessDeniedException extends ZKUploaderException
{
    protected $httpStatusCode = Response::HTTP_FORBIDDEN;

    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = 'Access denied', $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, Error::ACCESS_DENIED, $parameters, $previous);
    }
}
