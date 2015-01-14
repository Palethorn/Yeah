<?php

namespace Yeah\Fw\Application;

/**
 * Implements singleton pattern. Used for application request entry point
 * 
 * @property Router $router
 * @property Request $request
 * @property Response $response
 * @property \Yeah\Fw\Application\Autoloader $autoloader
 * @author David Cavar
 */
class App {

    private static $instance = null;
    private $request = null;
    private $response = null;
    private $router = null;
    private $session = null;
    private $logger = null;
    private $auth = null;
    private $options = null;
    private $autoloader = null;

    /**
     * Class constructor.
     * @param mixed $options Configuration options
     * 
     */
    private function __construct($options = array()) {
        $this->autoloader = $options['autoloader'];
        $this->options = $options;
        $this->createInstances();
    }

    /**
     * Creates application instances from application settings.
     */
    private function createInstances() {
        $this->registerAutoloaders();
        $this->router = new \Yeah\Fw\Routing\Router();
        $this->request = new \Yeah\Fw\Http\Request();
        $this->response = new \Yeah\Fw\Http\Response();

        (new $this->options['app']['database']['class']())->init($this->options['app']['database']['params']);

        $this->logger = new $this->options['app']['factories']['logger']['class']($this->options['app']['factories']['logger']['params']);
        $this->session = new \Yeah\Fw\Session\NullSessionHandler(); //new $this->options['app']['factories']['session']['class']($this->options['app']['database']);
        $this->auth = new \Yeah\Fw\Auth\NullAuth();// new $this->options['app']['factories']['auth']['class']($this->session);

        foreach($this->options['app']['factories'] as $key => $val) {
            if(!isset($val['params'])) {
                $val['params'] = array();
            }
            $this->$key = new $val['class']($val['params']);
        }
    }

    private function registerAutoloaders() {
        $this->autoloader->addIncludePath($this->options['app']['paths']['app_lib']);
        $this->autoloader->addIncludePath($this->options['app']['paths']['models']);
        $this->autoloader->addIncludePath($this->options['app']['paths']['controllers']);
    }

    /**
     * Begins chain execution
     */
    public function execute() {
        $route = $this->executeRouter();
        $route = $this->executeSecurity($route);
        $view = $this->executeAction($route);
        $this->executeRender($view);
    }

    /**
     * Fetches the route inside of a chain execution.
     * Don't invoke unless you know what you're doing.
     */
    private function executeRouter() {
        return $this->router->handle($this->request);
    }

    /**
     * Executes access checkup inside chain execution.
     * Don't invoke unless you know what you're doing.
     * 
     * @param mixed $route Route options
     * @return mixed Route options
     */
    private function executeSecurity(\Yeah\Fw\Routing\RouteInterface $route) {
        if($route->isSecure() && !$this->getAuth()->isAuthenticated()) {
            throw new \Yeah\Fw\Http\Exception\UnauthorizedHttpException();
        }
        return $route;
    }

    /**
     * Executes action inside chain execution.
     * Don't invoke unless you know what you're doing.
     * 
     * @param mixed $route Route options
     * @return \Yeah\Fw\Mvc\View Controller view object
     */
    private function executeAction(\Yeah\Fw\Routing\RouteInterface $route) {
        $controller = $route->getController();
        $method = $route->getAction() . '_action';
        $class = '\\' . ucfirst($controller) . 'Controller';
        $this->controller = new $class(array(
            'request' => $this->getRequest(),
            'response' => $this->getResponse(),
            'session' => $this->getSessionHandler(),
            'logger' => $this->getLogger(),
            'view' => array(
                'views_dir' => $this->options['app']['paths']['views']
            )
        ));

        if(!method_exists($this->controller, $method) || !is_callable($class . '::' . $method)) {
            throw new \Yeah\Fw\Http\Exception\NotFoundHttpException();
        }
        $this->controller->$method($this->request);
        $view = $this->controller->getView();
        return $view;
    }

    /**
     * Executes view rendering inside chain execution.
     * Don't invoke unless you know what you're doing.
     * 
     * @param \Yeah\Fw\Mvc\View $view Controller view object
     */
    private function executeRender($view) {
        if($this->getRequest()->getContentType() == 'application/json') {
            $this->response->writeJson($view->render());
        } else if($view != null) {
            $this->response->writePlain($view->render());
        } else {
            $this->response->writePlain($this->controller->getData());
        }
    }

    /**
     * getter for HTTP request object
     * 
     * @return \Yeah\Fw\Http\Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Getter for HTTP response object
     * 
     * @return \Yeah\Fw\Http\Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Getter for router object
     * 
     * @return \Yeah\Fw\Routing\Router
     */
    public function getRouter() {
        return $this->router;
    }

    /**
     * Getter for session handler object
     * 
     * @return SessionHandlerInterface
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * Setter for session handler object
     * 
     * @params \SessionHandlerInterface $session
     */
    public function setSession(\SessionHandlerInterface $session) {
        $this->session = $session;
    }

    /**
     * Getter for authentication object
     * 
     * @return \Yeah\Fw\Auth\AuthInterface
     */
    public function getAuth() {
        return $this->auth;
    }

    /**
     * Setter for authentication object
     * 
     * @param \Yeah\Fw\Auth\AuthInterface $auth
     */
    public function setAuth(\Yeah\Fw\Auth\AuthInterface $auth) {
        $this->auth = $auth;
    }

    /**
     * Getter for logger object
     * 
     * @return \Yeah\Fw\Logger\LoggerInterface
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     * Setter for logger object
     * @param \Yeah\Fw\Logger\LoggerInterface $logger
     */
    public function setLogger(\Yeah\Fw\Logger\LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function setAutoloader(Autoloader $autoloader) {
        $this->autoloader = $autoloader;
    }
    
    
    public function getAutoloader(Autoloader $autoloader) {
        $this->autoloader = $autoloader;
    }
    
    private function __clone() {
        
    }

    /**
     * Returns current application instance
     * 
     * @param mixed $options Application options
     * @return \Yeah\Fw\Application\App
     */
    public static function getInstance($options = null) {
        if(!isset(static::$instance)) {
            static::$instance = new App($options);
        }
        return static::$instance;
    }

    public function getOptions() {
        return $this->options;
    }

}
