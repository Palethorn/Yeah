<?php

namespace Yeah\Fw\Form;

abstract class FormAbstract extends \Yeah\Fw\ParameterHolder\SimpleParameterHolder implements \Yeah\Fw\Form\FormInterface {

    private $components = array();

    public function bind() {
        
    }

    public function getObject() {
        return $this->getOption('object');
    }

    public function render() {
        $html = '';
        foreach($this->components as $name => $component) {
            $html .= $component->render();
        }
        return $html;
    }

    public function save() {
        
    }

    public function setObject($object) {
        $this->setOption('object', $object);
    }

    public function validate() {
        
    }

    public function setComponent($name, $value) {
        $this->components[$name] = $value;
    }

    public function getComponent($name) {
        return $this->components[$name];
    }

}
