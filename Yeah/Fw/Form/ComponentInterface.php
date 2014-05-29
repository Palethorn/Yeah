<?php
namespace Yeah\Fw\Form;

interface ComponentInterface {
    function __construct($options);
    function setOption($key, $option);
    function getOption($key);
    function getAllOptions();
    function validate();
    function render();
}
