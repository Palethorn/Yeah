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

    function getValue($key);

    function setValue($key, $value);

    function setOption($key, $value);

    function getOption($key);

    function render();

    function save();

    function bind();
}
