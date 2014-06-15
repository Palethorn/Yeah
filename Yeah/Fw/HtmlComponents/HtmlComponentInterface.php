<?php

namespace Yeah\Fw\HtmlComponents;

/**
 * Exposes methods every HtmlComponent shoud implement
 * 
 * @author David Cavar
 */
interface HtmlComponentInterface {

    function __construct($options);

    function setOption($key, $option);

    function getOption($key);

    function getAllOptions();

    function validate();

    function render();
}
