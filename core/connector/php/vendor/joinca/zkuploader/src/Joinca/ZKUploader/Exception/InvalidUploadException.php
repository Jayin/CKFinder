<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;

/**
 * The "invalid upload" exception class.
 *
 * Thrown when an invalid file upload request was received.
 *
 * 
 */
class InvalidUploadException extends ZKUploaderException
{
    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param int        $code       the exception code
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = 'Invalid upload', $code = Error::UPLOADED_INVALID, $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, $code, $parameters, $previous);
    }
}
