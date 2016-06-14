<?php



namespace Joinca\ZKUploader\Exception;

use Joinca\ZKUploader\Error;

/**
 * The "invalid configuration" exception class.
 *
 * Thrown when the configuration file could not be found or is incomplete.
 *
 * 
 */
class InvalidConfigException extends ZKUploaderException
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
        parent::__construct($message, Error::INVALID_CONFIG, $parameters, $previous);
    }
}
