<?php

namespace Yeah\Fw\Auth;

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
