<?php

namespace Yeah\Fw\Mvc;

class TwigView implements ViewInterface {

    public $params = array();
    private $template = false;
    private $layout = false;
    private $content = '';
    private $twig;

    /**
     * Create new instance
     * 
     * @param string $views_dir
     * @param array $options
     */
    public function __construct($views_dir, $options = array()) {
        $this->views_dir = $views_dir;
        $loader = new \Twig_Loader_Filesystem($this->views_dir);
        $this->twig = new \Twig_Environment($loader, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function render() {
        return $this->content = $this->twig->render($this->template, $this->params);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($template = false) {
        $this->template = $template . '.twig';
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function with($key, $value) {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withLayout($layout) {
        $this->layout = $layout;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withParams($params) {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

}
