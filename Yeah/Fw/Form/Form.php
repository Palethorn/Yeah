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
        $this->setOption('id', $config['id']);
        $this->setOption('title', $config['title']);
        $this->setOption('action', $config['action']);
        $this->setOption('method', $config['method']);
        
        foreach($config['sections'] as $key => $section) {
            $sec = new FormSection();
            $sec->configure($section);
            $this->setComponent($key, $sec);
        }
    }

    public function setOption($key, $value) {
        
    }

    public function getObject() {
        return $this->getOption('object');
    }

    public function render() {
        $html = '';
        $html .= '<form id="' . $this->getOption('id') . '" method="' . $this->getOption('method') . '" action="' . $this->getOption('action') . '">';
        foreach($this->components as $section) {
            $html .= $section->render();
        }
        $html .= '</form>';
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
