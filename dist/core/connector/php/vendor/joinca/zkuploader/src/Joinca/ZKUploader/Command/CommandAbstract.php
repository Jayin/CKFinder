<?php



namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Exception\UnauthorizedException;
use Symfony\Component\HttpFoundation\Request;

/**
 * The base class for all Command classes.
 *
 */
abstract class CommandAbstract
{
    /**
     * The ZKUploader instance.
     *
     * @var ZKUploader $app
     */
    protected $app;

    /**
     * The request method - by default GET.
     *
     * @var string
     */
    protected $requestMethod = Request::METHOD_GET;

    /**
     * An array of permissions required by the command.
     *
     * @var array $requires
     */
    protected $requires = array();

    /**
     * Constructor.
     *
     * @param ZKUploader $app
     */
    public function __construct(ZKUploader $app)
    {
        $this->setContainer($app);
    }

    /**
     * Injects dependency injection container to the command scope.
     *
     * @param ZKUploader $app
     */
    public function setContainer(ZKUploader $app)
    {
        $this->app = $app;
    }

    /**
     * Checks permissions required by the command before it is executed.
     *
     * @throws \Exception if access is restricted.
     */
    public function checkPermissions()
    {
        if (!empty($this->requires)) {
            $workingFolder = $this->app->getWorkingFolder();

            $aclMask = $workingFolder->getAclMask();

            $requiredPermissionsMask = array_sum($this->requires);

            if (($aclMask & $requiredPermissionsMask) !== $requiredPermissionsMask) {
                throw new UnauthorizedException();
            }
        }
    }

    /**
     * Returns the name of the request method required by the command.
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * This method is not defined as abstract to allow for parameter injection.
     * @see Joinca\ZKUploader\CommandResolver::getArguments()
     */
    // public abstract function execute();
}
