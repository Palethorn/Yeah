<?php

namespace Yeah\Fw\Auth;

/**
 * Doesn't provide \Yeah\Fw\Auth\AuthInterface implementation.
 * Used for applications without authentication support.
 * 
 * @author David Cavar
 */
class NullAuth implements AuthInterface {

    function getUser() {
        return array();
    }

    function setUser($user) {
        
    }

    function isAuthenticated() {
        return true;
    }

    function setAuthenticated($value) {
        
    }

}
