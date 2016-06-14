<?php



namespace Joinca\ZKUploader\Event;

use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Command\CommandAbstract;

/**
 * The BeforeCommandEvent event class.
 */
class BeforeCommandEvent extends ZKUploaderEvent
{
    /**
     * The command name.
     *
     * @var string $commandObject
     */
    protected $commandName;

    /**
     * The object of the command to be executed.
     *
     * @var CommandAbstract $commandObject
     */
    protected $commandObject;

    /**
     * Constructor.
     *
     * @param ZKUploader        $app
     * @param string          $commandName
     * @param CommandAbstract $commandObject
     */
    public function __construct(ZKUploader $app, $commandName, CommandAbstract $commandObject)
    {
        $this->commandName = $commandName;
        $this->commandObject = $commandObject;

        parent::__construct($app);
    }

    /**
     * Returns the command object.
     *
     * @return CommandAbstract
     */
    public function getCommandObject()
    {
        return $this->commandObject;
    }

    /**
     * Sets the object of the command to be executed.
     *
     * @param CommandAbstract $commandObject
     */
    public function setCommandObject(CommandAbstract $commandObject)
    {
        $this->commandObject = $commandObject;
    }

    /**
     * Returns the name of the command.
     *
     * @return string command name
     */
    public function getCommandName()
    {
        return $this->commandName;
    }
}
