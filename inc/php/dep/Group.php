<?php

/**
 * Created by PhpStorm.
 * User: konrad
 * Date: 17.03.2017
 * Time: 23:15
 */
class Group
{
    private $permissions;
    private $name;

    function __construct($name, $permissions)
    {
        $this->name = $name;
        $this->permissions = $permissions;
    }

    function getPermissions()
    {
        return $this->permissions;
    }

    function hasPermission($permission)
    {
        return in_array('administrator', $this->permissions) || in_array($permission, $this->permissions);
    }

    function getName()
    {
        return $this->name;
    }
}