<?php

namespace Yeah\Fw\Mvc;

class TwigView implements ViewInterface {

    public $params = array();
    private $template = false;
    private $layout = false;
    private $content = '';
    private $twig;

    public function __construct($views_dir) {
        $this->views_dir = $views_dir;
        $loader = new \Twig_Loader_Filesystem($this->views_dir);
        $this->twig = new \Twig_Environment($loader);
    }

    public function render() {
        return $this->content = $this->twig->render($this->template, $this->params);
    }

    public function setTemplate($template = false) {
        $this->template = $template;
        return $this;
    }

    public function with($key, $value) {
        $this->params[$key] = $value;
        return $this;
    }

    public function withLayout($layout) {
        $this->layout = $layout;
        return $this;
    }

    public function withParams($params) {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

}
