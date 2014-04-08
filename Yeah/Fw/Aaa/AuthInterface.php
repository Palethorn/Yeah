<?php

namespace Yeah\Fw\Aaa;

interface AuthInterface {

    function getUser();

    function setUser($user);

    function isAuthenticated();

    function setAuthenticated($value);
}
