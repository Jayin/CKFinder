<?php


namespace Joinca\ZKUploader\Authentication;

/**
 * The AuthenticationInterface interface.
 *
 * An interface for authentication methods.
 */
interface AuthenticationInterface
{
    /**
     * @return bool `true` if the current user was successfully authenticated within ZKUploader.
     */
    public function authenticate();
}
