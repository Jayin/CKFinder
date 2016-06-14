<?php


namespace Joinca\ZKUploader\Response;

use Symfony\Component\HttpFoundation;

/**
 * The ZKUploader JSON response class.
 *
 * 
 */
class JsonResponse extends HttpFoundation\JsonResponse
{
    protected $rawData;

    public function __construct($data = null, $status = 200, $headers = array())
    {
        if (null === $data) {
            $data = new \stdClass();
        }

        parent::__construct($data, $status, $headers);

        $this->rawData = $data;
    }

    public function getData()
    {
        return $this->rawData;
    }

    public function setData($data = array())
    {
        $this->rawData = $data;

        return parent::setData($this->rawData);
    }

    public function withError($errorNumber, $errorMessage = null)
    {
        $errorData = array('number' => $errorNumber);

        if ($errorMessage) {
            $errorData['message'] = $errorMessage;
        }

        $data = (array) $this->rawData;

        $data = array('error' => $errorData) + $data;

        $this->setData($data);

        return $this;
    }
}
