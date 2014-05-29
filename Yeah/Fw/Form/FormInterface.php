<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormInterface
 *
 * @author david
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
