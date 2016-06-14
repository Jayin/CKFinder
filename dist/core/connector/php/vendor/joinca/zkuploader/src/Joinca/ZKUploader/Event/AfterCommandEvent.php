<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Symfony\Component\HttpFoundation\Response;

/**
 * The BeforeCommandEvent event class.
 */
class AfterCommandEvent extends ZKUploaderEvent
{
    /**
     * The command name.
     *
     * @var string $commandObject
     */
    protected $commandName;

    /**
     * The response object received from the command.
     *
     * @var Response $response
     */
    protected $response;

    /**
     * Constructor.
     *
     * @param ZKUploader $app
     * @param string   $commandName
     * @param Response $response
     */
    public function __construct(ZKUploader $app, $commandName, Response $response)
    {
        $this->commandName = $commandName;
        $this->response = $response;

        parent::__construct($app);
    }

    /**
     * Returns the response object received from the command.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the response to be returned.
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
