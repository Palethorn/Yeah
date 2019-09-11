<?php
namespace Yeah\Fw\Event;

class EventArgs {
    private $args;

    public function getArg($name) {
        return $this->args[$name];
    }

    public function setArg($name, $value) {
        $this->args[$name] = $value;
        return $this;
    }

    public function getArgs() {
        return $this->args;
    }
}