<?php

namespace Yeah\Fw\Mvc;

use Yeah\Fw\Http\Response;

class PhpView implements ViewInterface {

    public $params = array();
    private $template = false;
    private $layout = false;
    private $content = '';

    /**
     * Create new view instance
     * 
     * @param string $views_dir
     */
    public function __construct($views_dir, $options = array()) {
        $this->views_dir = $views_dir;
    }

    /**
     * Magic method for accessing anonymous object properties
     * 
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments) {
        if(strpos($name, 'setMeta') === 0) {
            $key = str_replace('setMeta', '', $name);
            $this->setMeta($key, $arguments[0]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($template = false) {
        $this->template = $template;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content) {
        $this->content = $content;
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
        foreach($params as $key => $val) {
            $this->$key = $val;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * 
     * @return page title
     */
    public function getTitle() {
        if(isset($this->title)) {
            return $this->title;
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function render() {
        $this->renderView();
        $this->renderLayout();
        return new Response($this->content);
    }

    /**
     * Render layout
     * 
     * @return string
     * @throws \Exception
     */
    public function renderLayout() {
        if(!$this->layout) {
            return;
        }
        if(!file_exists($this->views_dir . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $this->layout . '.php')) {
            throw new \Exception('Layout not found.', 500, null);
        }
        ob_start();
        require_once $this->views_dir . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $this->layout . '.php';
        $this->content = ob_get_contents();
        ob_end_clean();
    }

    /**
     * Render view
     * 
     * @return string
     * @throws \Exception
     */
    public function renderView() {
        if(!$this->template) {
            return;
        }

        $this->template = str_replace('/', DIRECTORY_SEPARATOR, $this->template);
        $view = $this->template . '.php';
        
        if(!file_exists($this->views_dir . DIRECTORY_SEPARATOR . $view)) {
            throw new \Exception('View not found.', 500, null);
        }
        
        ob_start();
        require_once $this->views_dir . DIRECTORY_SEPARATOR . $view;
        $this->content = ob_get_contents();
        ob_end_clean();
    }

}
