<?php

namespace Yeah\Fw\HtmlComponents;

abstract class HtmlComponentAbstract extends \Yeah\Fw\ParameterHolder\SimpleParameterHolder implements HtmlComponentInterface {

    public function configure($options) {
        foreach($options as $key => $option) {
            $this->setOption($key, $option);
        }
    }

    public function validate() {
        
    }

}
