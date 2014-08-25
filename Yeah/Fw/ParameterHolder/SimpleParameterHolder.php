<?php

namespace Yeah\Fw\ParameterHolder;

class SimpleParameterHolder implements ParameterHolderInterface {

    private $options = array();

    public function getOption($key) {
        if(isset($this->options[$key])) {
            return $this->options[$key];
        }
        return false;
    }

    public function removeOption($key) {
        unset($this->options[$key]);
    }
    
    public function getAllOptions() {
        return $this->options;
    }

    public function setOption($key, $value) {
        $this->options[$key] = $value;
    }

}
