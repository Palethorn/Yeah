<?php
namespace Yeah\Fw\Mvc;

class Closure implements CallableInterface {
    private $closure = null;

    public function __construct(\Closure $closure) {
        $this->closure = $closure;
    }
    
    public function call() {
        return ($this->closure)();
    }
}
