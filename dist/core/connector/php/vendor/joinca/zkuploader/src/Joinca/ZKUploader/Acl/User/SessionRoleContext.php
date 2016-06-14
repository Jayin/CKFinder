<?php


namespace Joinca\ZKUploader\Acl\User;


class SessionRoleContext implements RoleContextInterface
{

    protected $sessionRoleField;

    public function __construct($sessionRoleField)
    {
        $this->sessionRoleField = $sessionRoleField;
    }

    public function getRole()
    {
        if (strlen($this->sessionRoleField) && isset($_SESSION[$this->sessionRoleField])) {
            return (string) $_SESSION[$this->sessionRoleField];
        }

        return null;
    }
}
