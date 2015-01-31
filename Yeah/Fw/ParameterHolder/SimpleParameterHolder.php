<?php

namespace Yeah\Fw\ParameterHolder;

class SimpleParameterHolder implements ParameterHolderInterface {

    private $options = array();

    /**
     * {@inheritdoc}
     */
    public function getOption($key) {
        if(isset($this->options[$key])) {
            return $this->options[$key];
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function removeOption($key) {
        unset($this->options[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions() {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($key, $value) {
        $this->options[$key] = $value;
    }

}
