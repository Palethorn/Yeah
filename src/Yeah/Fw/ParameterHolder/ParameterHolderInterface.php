<?php

namespace Yeah\Fw\ParameterHolder;

interface ParameterHolderInterface {

    /**
     * Retrieve option under specified key
     */
    function getOption($key);

    /**
     * Set option under specified key
     */
    function setOption($key, $value);

    /**
     * Unset option under specified key
     */
    function removeOption($key);

    /**
     * Retrieve collection of options
     */
    function getAllOptions();
}
