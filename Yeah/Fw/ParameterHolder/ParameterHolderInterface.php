<?php
namespace Yeah\Fw\ParameterHolder;

interface ParameterHolderInterface {
    function getOption($key);
    function setOption($key, $value);
    function removeOption($key);
    function getAllOptions();
}
