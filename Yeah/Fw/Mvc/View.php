<?php

namespace Yeah\Fw\Mvc;

class View {

    public $params = array();
    private $name = false;
    private $layout = false;
    private $content = '';

    public function __construct(string $views_dir) {
        $this->views_dir = $views_dir;
    }

    public function __call($name, $arguments) {
        if(strpos($name, 'setMeta') === 0) {
            $key = str_replace('setMeta', '', $name);
            $this->setMeta($key, $arguments[0]);
        }
    }

    /*
     * @return View
     */

    public function setTemplate($template = false) {
        $this->name = $template;
        return $this;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function withLayout($layout) {
        $this->layout = $layout;
        return $this;
    }

    public function withParams($params) {
        $this->params = $params;
        return $this;
    }

    public function with($key, $value) {
        $this->$key = $value;
        return $this;
    }

    /**
     * 
     * @param string $message
     * @param string $type
     * @return View
     */
    public function setMessage($message, $type = 'info') {
        $this->message['text'] = $message;
        $this->message['type'] = $type;
        return $this;
    }

    /**
     * 
     * @param string $title
     * @return View
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        if(isset($this->title)) {
            return $this->title;
        }
        return '';
    }

    public function render() {
        $this->renderView();
        $this->renderLayout();
        return $this->content;
    }

    public function renderLayout() {
        if(!$this->layout) {
            return;
        }
        if(!file_exists($this->views_dir . DS . 'layouts' . DS . $this->layout . '.php')) {
            throw new \Exception('Layout not found.', 500, null);
        }
        ob_start();
        require_once $this->views_dir . DS . 'layouts' . DS . $this->layout . '.php';
        $this->content = ob_get_contents();
        ob_end_clean();
    }

    public function renderView() {
        if(!$this->name) {
            return;
        }
        $this->name = str_replace('/', DS, $this->name);
        $view = $this->name . '.php';
        if(!file_exists($this->views_dir . DS . $view)) {
            throw new \Exception('View not found.', 500, null);
        }
        ob_start();
        require_once $this->views_dir . DS . $view;
        $this->content = ob_get_contents();
        ob_end_clean();
    }

}
