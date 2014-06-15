<?php
namespace Yeah\Fw\Form;

abstract class FormAbstract implements \Yeah\Fw\Form\FormInterface {
    public function bind() {
        
    }

    public function getObject() {
        return $this->object;
    }

    public function getOption($key) {
        
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
        $this->object = $object;
    }

    public function setOption($key, $value) {
        
    }

    public function validate() {
        
    }

}
