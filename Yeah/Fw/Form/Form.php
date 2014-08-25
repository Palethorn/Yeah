<?php

namespace Yeah\Fw\Form;

/**
 * @property Yeah\Fw\Form\FormSection[] $components Form section collection
 */
class Form extends \Yeah\Fw\ParameterHolder\SimpleParameterHolder implements FormInterface {

    private $components = array();

    public function bind() {
        
    }

    public function configure($config) {
        foreach($config as $section) {
            $sec = new FormSection();
            $sec->configure($section);
            $this->setComponent($section['title'], $sec);
        }
    }

    public function setOption($key, $value) {
        
    }

    public function getObject() {
        return $this->getOption('object');
    }

    public function render() {
        $html = '<div>';
        foreach($this->components as $section) {
            $html .= $section->render();
        }
        $html .= '</div>';
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
