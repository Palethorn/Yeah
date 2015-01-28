<?php

namespace Yeah\Fw\HtmlComponents;

/**
 * Exposes methods every HtmlComponent shoud implement
 * 
 * @author David Cavar
 */
interface HtmlComponentInterface {

    function configure($options);

    function validate();

    function render();
}
