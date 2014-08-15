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

    public function setOption($key, $value) {
        $this->options[$key] = $value;
    }

    public function getAllOptions() {
        return $this->options;
    }

}
