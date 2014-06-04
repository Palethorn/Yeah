<?php

namespace Yeah\Fw\Auth;

/**
 * Interface for authentication class implementation
 * 
 * @author David Cavar
 */
interface AuthInterface {

    /**
     * Return session user object
     */
    function getUser();

    /**
     * Set session user object
     * 
     * @param User $user User object
     */
    function setUser($user);

    /**
     * Checks if user is authenticated
     * 
     * @return bool Value indicating if the user is authenticated
     */
    function isAuthenticated();

    /**
     * Set user authenticated flag
     * 
     * @param bool $value Param indicating if the user is authenticated
     */
    function setAuthenticated($value);
}
