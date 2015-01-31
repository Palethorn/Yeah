<?php

namespace Yeah\Fw\Application;

/**
 * Implements singleton pattern. Used for application request entry point
 * 
 * @property \Yeah\Fw\Error\ErrorHandler $error_handler
 * @property \Yeah\Fw\Logger\LoggerInterface $logger
 * @property \SessionHandlerInterface $session
 * @property \Yeah\Fw\Auth\AuthInterface $auth
 * @property \Yeah\Fw\Mvc\View\ViewInterface $view
 * @property \Yeah\Fw\Application\DependencyContainer $dc
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
    protected $view = null;
    protected $dc = null;

    /**
     * Class constructor.
     * @param mixed $options Configuration options
     * 
     */
    public function __construct() {
        $this->registerAutoloaders();
        $this->router = new \Yeah\Fw\Routing\Router();
        $this->request = new \Yeah\Fw\Http\Request();
        $this->response = new \Yeah\Fw\Http\Response();
        $this->error_handler = new \Yeah\Fw\Error\ErrorHandler();
        $this->loadRoutes();
        $this->dc = new DependencyContainer();
    }

    /**
     * Register autoloader paths for probing
     */
    protected function registerAutoloaders() {
        require_once $this->getLibDir() . DS . 'Yeah' . DS . 'Fw' . DS . 'Application' . DS . 'Autoloader.php';
        $this->autoloader = new Autoloader();
        $this->autoloader->addIncludePath($this->getLibDir());
        $this->autoloader->addIncludePath($this->getModelsDir());
        $this->autoloader->addIncludePath($this->getControllersDir());
        $this->autoloader->register();
    }

    /**
     * Load additional routes
     */
    public function loadRoutes() {
        $routes_location = $this->getBaseDir() . DS . 'config' . DS . 'routes.php';
        if(file_exists($routes_location)) {
            require_once $routes_location;
        }
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
        if($response instanceof \Yeah\Fw\Mvc\ViewInterface) {
            $this->response->writePlain($response->render());
        }
        if(is_array($response)) {
            $layout = isset($response['layout']) ? $response['layout'] : 'default';
            $template = isset($response['template']) ? $response['template'] : ($this->request->getParameter('action') ? $this->request->getParameter('action') : 'index');
            $this->response
                    ->writePlain(
                            $this->getView()
                            ->setTemplate($template)
                            ->withLayout($layout)
                            ->withParams($response)
                            ->render()
            );
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
     * 
     * @return \Yeah\Fw\Mvc\ViewInterface
     */
    public function getView() {
        if(!$this->view) {
            $this->view = new \Yeah\Fw\Mvc\PhpView($this->getViewsDir());
        }
        return $this->view;
    }

    /**
     * Setter for logger object
     * @param \Yeah\Fw\Logger\LoggerInterface $logger
     */
    public function setLogger(\Yeah\Fw\Logger\LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Set custom autoloader
     * 
     * @param \Yeah\Fw\Application\Autoloader $autoloader
     */
    public function setAutoloader(Autoloader $autoloader) {
        $this->autoloader = $autoloader;
    }

    /**
     * Get application autoloader
     * @param \Yeah\Fw\Application\Autoloader $autoloader
     */
    public function getAutoloader(Autoloader $autoloader) {
        $this->autoloader = $autoloader;
    }

    /**
     * Returns path for application root dir
     * @return string
     */
    public function getBaseDir() {
        return dirname(__FILE__) . DS . '..' . DS . '..' . DS . '..' . DS . '..';
    }

    /**
     * Returns path for library directory
     * @return string
     */
    public function getLibDir() {
        return $this->getBaseDir() . DS . 'lib';
    }

    /**
     * Returns path for public directory
     * 
     * @return string
     */
    public function getWebDir() {
        return $this->getBaseDir() . DS . 'web';
    }

    /**
     * Return path for cache directory
     * 
     * @return string
     */
    public function getCacheDir() {
        return $this->getBaseDir() . DS . 'cache';
    }

    /**
     * Returns path for logs directory
     * 
     * @return string
     */
    public function getLogDir() {
        return $this->getBaseDir() . DS . 'log';
    }

    /**
     * Returns path for controllers directory
     * 
     * @return string
     */
    public function getControllersDir() {
        return $this->getBaseDir() . DS . 'controllers';
    }

    /**
     * Returns path for models directory
     * 
     * @return string
     */
    public function getModelsDir() {
        return $this->getBaseDir() . DS . 'models';
    }

    /**
     * Returns path for views directors
     * 
     * @return string
     */
    public function getViewsDir() {
        return $this->getBaseDir() . DS . 'views';
    }

    private function __clone() {
        
    }

    /**
     * Adds new simple route for GET HTTP method
     * 
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routeGet($url, $method, $secure = false) {
        \Yeah\Fw\Routing\Router::add($url, array(
            'route_request_handler' => 'Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler',
            'secure' => $secure,
            'method' => $method,
            'http_method' => 'GET'
        ));
    }

    /**
     * Adds new simple route for POST HTTP method
     * 
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routePost($url, $method, $secure = false) {
        \Yeah\Fw\Routing\Router::add($url, array(
            'route_request_handler' => 'Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler',
            'secure' => $secure,
            'method' => $method,
            'http_method' => 'POST'
        ));
    }

    /**
     * Adds new simple route for PUT HTTP method
     * 
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routePut($url, $method, $secure = false) {
        \Yeah\Fw\Routing\Router::add($url, array(
            'route_request_handler' => 'Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler',
            'secure' => $secure,
            'method' => $method,
            'http_method' => 'PUT'
        ));
    }

    /**
     * Adds new simple route for DELETE HTTP method
     * 
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routeDelete($url, $method, $secure = false) {
        \Yeah\Fw\Routing\Router::add($url, array(
            'route_request_handler' => 'Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler',
            'secure' => $secure,
            'method' => $method,
            'http_method' => 'DELETE'
        ));
    }

    /**
     * Returns dependency container object
     * 
     * @return DependencyContainer
     */
    public function getDependencyContainer() {
        return $this->dc;
    }

    /**
     * Returns current application instance
     * 
     * @param mixed $options Application options
     * @return \Yeah\Fw\Application\App
     */
    public static function getInstance() {
        if(!isset(static::$instance)) {
            static::$instance = new App();
        }
        return static::$instance;
    }

}
