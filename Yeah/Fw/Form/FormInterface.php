<?php

namespace Yeah\Fw\Form;

/**
 * Exposes methods every form class should implement
 *
 * @author David Cavar
 */
interface FormInterface {

    function validate();

    function setObject($object);

    function getObject();

    function render();

    function save();

    function bind();
    
    function configure();
    
    function getComponent($name);
    function setComponent($name, $value);
}
