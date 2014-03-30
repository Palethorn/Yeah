<?php

namespace Yeah\Fw\Application;

class App {

    private $options = array();
    public function __construct($options) {
        $this->options = $options;
    }
    
    public function execute() {
        $context = Context::getInstance($this->options);
        try {
            $context->execute();
            $context->getLogger()->i('Peak memory usage: ' . memory_get_peak_usage() / 1024 / 1024 . " MB");
        } catch (Exception $e) {
            $w = new Yeah\Fw\Mvc\View('exception');
            $w->withLayout('default');
            $w->withParams(array('error' => $e->getMessage()));
            $context->getResponse()->setResponseCode($e->getCode());
            $context->getResponse()->write($w->render());
        }
    }

}
