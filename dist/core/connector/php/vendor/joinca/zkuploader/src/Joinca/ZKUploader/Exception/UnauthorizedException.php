<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;
use Symfony\Component\HttpFoundation\Response;

/**
 * The "unauthorized exception" class.
 *
 * Thrown when ACL configuration does not allow for an operation,
 * e.g. uploading a file to a folder without the `FILE_CREATE` permission.
 *
 * 
 */
class UnauthorizedException extends ZKUploaderException
{
    protected $httpStatusCode = Response::HTTP_UNAUTHORIZED;

    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = 'Unauthorized', $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, Error::UNAUTHORIZED, $parameters, $previous);
    }
}
