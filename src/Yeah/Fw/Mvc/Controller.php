<?php
namespace Yeah\Fw\Mvc;

use Yeah\Fw\Mvc\Exception\MethodNotFoundException;

class Controller implements CallableInterface {

    public function __construct(string $method_name) {
        $this->method_name = $method_name;
    }

    public function call() {
        if(method_exists($this, $this->method_name)) {
            return $this->{$this->method_name}();
        }

        throw new MethodNotFoundException(sprintf('Method %s of class %s not found', $this->method_name, self::class));
    }
}
