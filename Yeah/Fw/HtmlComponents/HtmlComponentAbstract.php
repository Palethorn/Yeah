<?php

namespace Yeah\Fw\HtmlComponents;

abstract class HtmlComponentAbstract implements HtmlComponentInterface {

    private $options = array();
    private $value = null;

    public function __construct($options) {
        foreach($options as $key => $option) {
            $this->setOption($key, $option);
        }
    }

    public function getAllOptions() {
        return $this->options;
    }

    public function getOption($key) {
        if(isset($this->options[$key])) {
            return $this->options[$key];
        }
        return false;
    }

    public function setOption($key, $option) {
        $this->options[$key] = $option;
    }

    public function validate() {
        
    }

}
