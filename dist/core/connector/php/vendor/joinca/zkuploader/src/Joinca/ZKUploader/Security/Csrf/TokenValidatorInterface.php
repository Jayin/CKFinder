<?php


namespace Joinca\ZKUploader\Security\Csrf;

use Symfony\Component\HttpFoundation\Request;

/**
 * The TokenValidatorInterface interface.
 *
 * An interface for CSRF token validators.
 */
interface TokenValidatorInterface
{
    /**
     * Checks if the request contains a valid CSRF token.
     *
     * @param Request $request
     *
     * @return bool `true` if the token is valid, `false` otherwise.
     */
    public function validate(Request $request);
}
