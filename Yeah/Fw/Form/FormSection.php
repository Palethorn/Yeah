<?php

namespace Yeah\Fw\Form;

class FormSection extends \Yeah\Fw\ParameterHolder\SimpleParameterHolder implements FormInterface {

    private $components = array();

    public function configure($section) {
        $this->setOption('title', $section['title']);
        foreach($section['components'] as $key => $component) {
            $comp = new $component['class']($component);
            $this->setComponent($key, $comp);
        }
    }

    public function bind() {
        
    }

    public function getComponent($name) {
        if(isset($this->components[$name])) {
            return $this->components[$name];
        }
        return false;
    }

    public function getObject() {
        
    }

    public function render() {
        $html = '<div>' .
                '<div>' .
                $this->getOption('title') .
                '   </div>';
        foreach($this->components as $key => $component) {
            $html .= $component->render();
        }
        $html .= '</div>';
        return $html;
    }

    public function save() {
        
    }

    public function setComponent($name, $value) {
        $this->components[$name] = $value;
    }

    public function setObject($object) {
        
    }

    public function validate() {
        
    }

}
