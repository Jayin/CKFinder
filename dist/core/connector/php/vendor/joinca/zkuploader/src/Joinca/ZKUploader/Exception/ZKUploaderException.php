<?php



namespace Joinca\ZKUploader\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * The base ZKUploader exception class.
 *
 * 
 */
class ZKUploaderException extends \Exception
{
    /**
     * An array of parameters passed for replacements used in translation.
     *
     * @var array $parameters
     */
    protected $parameters;

    /**
     * HTTP response status code.
     * @var int
     */
    protected $httpStatusCode = Response::HTTP_BAD_REQUEST;

    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param int        $code       the exception code
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = null, $code = 0, $parameters = array(), \Exception $previous = null)
    {
        $this->parameters = $parameters;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns parameters used for replacements during translation.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the HTTP status code for this exception.
     *
     * @return int HTTP status code for exception
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Sets the HTTP status code for this exception.
     *
     * @param int $httpStatusCode
     *
     * @return $this
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;

        return $this;
    }
}
