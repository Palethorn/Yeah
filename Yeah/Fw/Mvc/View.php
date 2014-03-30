<?php
namespace Yeah\Fw\Mvc;

class View {

    public $params = array();

    public function __construct($view = false) {
        $this->name = $view;
    }

    public function __call($name, $arguments) {
        if(strpos($name, 'setMeta') === 0) {
            $key = str_replace('setMeta', '', $name);
            $this->setMeta($key, $arguments[0]);
        }
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

    public function setMeta($key, $value) {
        
    }
    
    public function getTitle() {
        if(isset($this->title)) {
            return $this->title;
        }
        return '';
    }

    public function render() {
        if($this->name) {
            $this->includeView();
        }
        if(isset($this->layout)) {
            ob_start();
            if(!file_exists(\Yeah\Fw\Application\Config::get('views') . DS . 'layouts' . DS . $this->layout . '.php')) {
                ob_end_clean();
                throw new \Exception('Layout not found.', 500, null);
            }
            require_once \Yeah\Fw\Application\Config::get('views') . DS . 'layouts' . DS . $this->layout . '.php';
            $this->content = ob_get_contents();
            ob_end_clean();
        }
        return $this->content;
    }

    public function includeView() {
        $this->name = str_replace('/', DS, $this->name);
        $view = $this->name . '.php';
        ob_start();
        if(!file_exists(\Yeah\Fw\Application\Config::get('views') . DS . $view)) {
            ob_end_clean();
            throw new \Exception('View not found.', 500, null);
        }
        require_once \Yeah\Fw\Application\Config::get('views') . DS . $view;
        $this->content = ob_get_contents();
        ob_end_clean();
    }
}
