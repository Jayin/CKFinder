<?php


namespace Joinca\ZKUploader\Authentication;

/**
 * The CallableAuthentication class.
 *
 * Basic ZKUploader authentication class that authenticates the current user
 * using a PHP callable provided in the configuration file.
 */
class CallableAuthentication implements AuthenticationInterface
{
    /**
     * @var callable
     */
    protected $authCallable;

    /**
     * Constructor.
     *
     * @param callable $authCallable
     */
    public function __construct(callable $authCallable)
    {
        $this->authCallable = $authCallable;
    }

    /**
     * @return bool `true` if the current user was successfully authenticated within ZKUploader.
     */
    public function authenticate()
    {
        return call_user_func($this->authCallable);
    }
}
