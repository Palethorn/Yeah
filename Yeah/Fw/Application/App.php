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

    protected static $instance = null;
    protected $request = null;
    protected $response = null;
    protected $router = null;
    protected $autoloader = null;
    protected $error_handler = null;
    protected $logger = null;
    protected $session = null;
    protected $auth = null;

    /**
     * Class constructor.
     * @param mixed $options Configuration options
     * 
     */
    protected function __construct($base_dir) {
        $this->base_dir = $base_dir;
        $this->registerAutoloaders();
        $this->router = new \Yeah\Fw\Routing\Router();
        $this->request = new \Yeah\Fw\Http\Request();
        $this->response = new \Yeah\Fw\Http\Response();
        $this->error_handler = new \Yeah\Fw\Error\ErrorHandler();
    }

    private function registerAutoloaders() {
        require_once $this->getLibDir() . DS . 'Yeah' . DS . 'Fw' . DS . 'Application' . DS . 'Autoloader.php';
        $this->autoloader = new Autoloader();
        $this->autoloader->addIncludePath($this->getLibDir());
        $this->autoloader->addIncludePath($this->getModelsDir());
        $this->autoloader->addIncludePath($this->getControllersDir());
        $this->autoloader->register();
    }

    /**
     * Begins chain execution
     */
    public function execute() {
        $route = $this->executeRouter();
        $route = $this->executeSecurity($route);
        $action_result = $this->executeAction($route);
        $this->executeRender($action_result);
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
    private function executeSecurity(\Yeah\Fw\Routing\Route\RouteInterface $route) {
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
    private function executeAction(\Yeah\Fw\Routing\Route\RouteInterface $route) {
        return $route->execute(
                        $this->getRequest(), $this->getResponse(), $this->getSession(), $this->getAuth()
        );
    }

    /**
     * Executes view rendering inside chain execution.
     * Don't invoke unless you know what you're doing.
     * 
     * @param \Yeah\Fw\Mvc\View $view Controller view object
     */
    private function executeRender($response) {
        if($response instanceof \Yeah\Fw\Mvc\View) {
            $this->response->writePlain($response->render());
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
        if($this->session == null) {
            $this->session = new \Yeah\Fw\Session\NullSessionHandler();
        }
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
        if($this->auth == null) {
            $this->auth = new \Yeah\Fw\Auth\NullAuth();
        }
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
        if($this->logger == null) {
            $this->logger = new \Yeah\Fw\Logger\NullLogger();
        }
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

    public function getBaseDir() {
        return $this->base_dir;
    }

    public function getLibDir() {
        return $this->base_dir . DS . 'lib';
    }

    public function getWebDir() {
        return $this->base_dir . DS . 'web';
    }

    public function getCacheDir() {
        return $this->base_dir . DS . 'cache';
    }

    public function getLogDir() {
        return $this->base_dir . DS . 'log';
    }

    public function getControllersDir() {
        return $this->base_dir . DS . 'controllers';
    }

    public function getModelsDir() {
        return $this->base_dir . DS . 'models';
    }

    public function getViewsDir() {
        return $this->base_dir . DS . 'views';
    }

    private function __clone() {
        
    }

    public function routeGet($url, $method, $secure = false) {
        \Yeah\Fw\Routing\Router::add($url, array(
            'route_request_handler' => 'Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler',
            'secure' => $secure,
            'method' => $method,
            'http_method' => 'GET'
        ));
    }

    public function routePost($url, $method, $secure = false) {
        \Yeah\Fw\Routing\Router::add($url, array(
            'route_request_handler' => 'Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler',
            'secure' => $secure,
            'method' => $method,
            'http_method' => 'POST'
        ));
    }

    public function routePut($url, $method, $secure = false) {
        \Yeah\Fw\Routing\Router::add($url, array(
            'route_request_handler' => 'Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler',
            'secure' => $secure,
            'method' => $method,
            'http_method' => 'PUT'
        ));
    }

    public function routeDelete($url, $method, $secure = false) {
        \Yeah\Fw\Routing\Router::add($url, array(
            'route_request_handler' => 'Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler',
            'secure' => $secure,
            'method' => $method,
            'http_method' => 'DELETE'
        ));
    }

    /**
     * Returns current application instance
     * 
     * @param mixed $options Application options
     * @return \Yeah\Fw\Application\App
     */
    public static function getInstance($base_dir = null) {
        if(!isset(static::$instance)) {
            static::$instance = new App($base_dir);
        }
        return static::$instance;
    }

}
