<?php

namespace Yeah\Fw\Application;

/**
 * @property Request $request Description
 * @property Response $response Description
 * @property Router $router Desc
 * @property SessionHandlerInterface $session Description
 * @property Logger $logger Description
 * @property DatabaseAuth $auth Desc
 */
class App {

    private static $instance = null;
    private $request = null;
    private $response = null;
    private $router = null;
    private $session = null;
    private $logger = null;
    private $auth = null;

    private function __construct($options = array()) {
        $this->options = $options;
        $this->options['app'] = require_once $options['paths']['app_dir'] . DS . 'config' . DS . 'AppConfiguration.php';
        $this->createInstances();
    }

    public function createInstances() {
        (new \Yeah\Fw\Application\Autoloader())->setIncludePath($this->options['app']['paths']['models'])->register();
        (new \Yeah\Fw\Application\Autoloader())->setIncludePath($this->options['app']['paths']['controllers'])->register();
        (new $this->options['app']['database']['adapter']())->init($this->options['app']['database']);
        $this->logger = new $this->options['app']['factories']['logger']['class']($this->options['app']['factories']['logger']);
        $this->session = new $this->options['app']['factories']['session_handler']['class']($this->options['app']);
        $this->request = new $this->options['app']['factories']['request']['class']($this->options['app']['factories']['request']);
        $this->response = new $this->options['app']['factories']['response']['class']($this->options['app']['factories']['response']);
        $this->router = new $this->options['app']['factories']['router']['class']($this->options['app']['factories']['router']);
        $this->auth = new $this->options['app']['factories']['auth']['class'](array('session_handler' => $this->session));
    }

    public function execute() {
        // Chain execution
        $ret = $this->executeRouter();
        $ret = $this->executeSecurity($ret);
        $ret = $this->executeAction($ret);
        $this->executeRender($ret);
    }

    /**
     * 
     */
    public function executeRouter() {
        return $this->router->handle($this->request);
    }

    /**
     * 
     */
    public function executeSecurity($route) {
        if(isset($route['secure']) && $route['secure'] == true) {
            $auth = $this->getAuth();
            if(!$auth->isAuthenticated()) {
                $this->getResponse()->setFlash('You are not logged in!')->redirect($this->options['app']['default_login']);
            }
        }
        return $route;
    }

    /**
     * 
     */
    public function executeAction($route) {
        $controller = $route['controller'];
        $method = $route['action'] . '_action';
        $class = '\\' . ucfirst($controller) . 'Controller';
        $this->controller = new $class(array(
            'request' => $this->request,
            'response' => $this->response,
            'session' => $this->getSessionHandler(),
            'logger' => $this->getLogger(),
            'view' => array(
                'views_dir' => $this->options['app']['paths']['views']
            )
        ));

        if(!method_exists($this->controller, $method) || !is_callable($class . '::' . $method)) {
            throw new \Exception('Not Found', 404, null);
        }
        $this->controller->$method($this->request);
        $view = $this->controller->getView();
        return $view;
    }

    /**
     * 
     */
    public function executeRender($view) {
        if($this->getRequest()->getContentType() == 'application/json') {
            $this->response->writeJson($view->render());
        } else if($view != null) {
            $this->response->writePlain($view->render());
        } else {
            $this->response->writePlain($this->controller->getData());
        }
    }

    /**
     * 
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * 
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * 
     * @return SessionHandlerInterface
     */
    public function getSessionHandler() {
        return $this->session;
    }

    /**
     * 
     * @return Auth
     */
    public function getAuth() {
        return $this->auth;
    }

    /**
     * 
     * @return \Yeah\Fw\Logger\LoggerInterface
     */
    public function getLogger() {
        return $this->logger;
    }

    private function __clone() {
        
    }

    /**
     * 
     * @return Context
     */
    public static function getInstance($options = null) {
        if(!isset(static::$instance)) {
            static::$instance = new App($options);
        }
        return static::$instance;
    }

}
