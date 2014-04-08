<?php

namespace Yeah\Fw\Aaa;

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
