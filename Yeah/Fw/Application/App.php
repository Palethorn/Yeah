<?php

namespace Yeah\Fw\Application;

/**
 * Implements singleton pattern. Used for application request entry point
 *
 * @property \\Yeah\\Fw\\Error\\ErrorHandler $error_handler
 * @property \\Yeah\\Fw\\Logger\\LoggerInterface $logger
 * @property \\SessionHandlerInterface $session
 * @property \\Yeah\\Fw\\Auth\\AuthInterface $auth
 * @property \\Yeah\\Fw\\Mvc\\View\\ViewInterface $view
 * @property \\Yeah\\Fw\\Application\\DependencyContainer $dc
 * @property Router $router
 * @property Request $request
 * @property Response $response
 * @property \\Yeah\\Fw\\Application\\Autoloader $autoloader
 * @property \\Yeah\\Fw\\Application\\Config $config
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
    protected $app_name = '';
    protected $config = null;

    /**
     * Class constructor.
     * @param mixed $options Configuration options
     *
     */
    public function __construct($app_name, $env = 'prod', $config = array('prod' => array())) {
        $this->app_name = $app_name;
        require_once 'Config.php';
        $this->config = new Config($config[$env]);
        $this->registerAutoloaders();
        $this->configureAutoloadCache();
        $this->error_handler = new \Yeah\Fw\Error\ErrorHandler();
        $this->router = new \Yeah\Fw\Routing\Router();
        $this->request = new \Yeah\Fw\Http\Request();
        $this->response = new \Yeah\Fw\Http\Response();
        $this->dc = new DependencyContainer();
        $this->configureServices();
        $this->loadRoutes();
        self::$instance = $this;
    }

    public function getAppName() {
        return $this->app_name;
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

    public function configureAutoloadCache() {
        $this->autoloader->setCache(new \Yeah\Fw\Cache\NullCache());
    }

    /**
     * Load additional routes
     */
    public function loadRoutes() {
        $routes_location = $this->getBaseDir() . DS . $this->getAppName() . DS . 'config' . DS . 'routes.php';
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
     * @return \\Yeah\\Fw\\Mvc\\View Controller view object
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
     * @param \\Yeah\\Fw\\Mvc\\View $view Controller view object
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
     * @return \\Yeah\\Fw\\Http\\Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Getter for HTTP response object
     *
     * @return \\Yeah\\Fw\\Http\\Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Getter for router object
     *
     * @return \\Yeah\\Fw\\Routing\\Router
     */
    public function getRouter() {
        return $this->router;
    }

    public function getDatabaseConfig() {
        return $this->getDependencyContainer()->get('db_config');
    }

    /**
     * Getter for session handler object
     *
     * @return SessionHandlerInterface
     */
    public function getSession() {
        return $this->getDependencyContainer()->get('session');
    }

    public function getAuth() {
        return $this->getDependencyContainer()->get('auth');
    }

    /**
     * Getter for logger object
     *
     * @return \\Yeah\\Fw\\Logger\\LoggerInterface
     */
    public function getLogger() {
        return $this->getDependencyContainer()->get('logger');
    }

    /**
     *
     * @return \\Yeah\\Fw\\Mvc\\ViewInterface
     */
    public function getView() {
        return $this->getDependencyContainer()->get('view');
    }

    /**
     * Get application autoloader
     * @param \\Yeah\\Fw\\Application\\Autoloader $autoloader
     */
    public function getAutoloader() {
        return $this->autoloader;
    }

    /**
     * Returns path for application root dir
     * @return string
     */
    public function getBaseDir() {
        if($this->config->base_dir) {
            return $this->config->base_dir;
        }
        return dirname(__FILE__) . DS . '..' . DS . '..' . DS . '..' . DS . '..';
    }

    /**
     * Returns path for library directory
     * @return string
     */
    public function getLibDir() {
        if($this->config->lib_dir) {
            return $this->config->lib_dir;
        }
        return $this->getBaseDir() . DS . 'lib';
    }

    /**
     * Returns path for public directory
     *
     * @return string
     */
    public function getWebDir() {
        if($this->config->web_dir) {
            return $this->config->web_dir;
        }
        return $this->getBaseDir() . DS . 'web';
    }

    /**
     * Return path for cache directory
     *
     * @return string
     */
    public function getCacheDir() {
        if($this->config->cache_dir) {
            return $this->config->cache_dir;
        }
        return $this->getBaseDir() . DS . 'cache';
    }

    /**
     * Returns path for logs directory
     *
     * @return string
     */
    public function getLogDir() {
        if($this->config->log_dir) {
            return $this->config->log_dir;
        }
        return $this->getBaseDir() . DS . 'log';
    }

    /**
     * Returns path for controllers directory
     *
     * @return string
     */
    public function getControllersDir() {
        if($this->config->controllers_dir) {
            return $this->config->controllers_dir;
        }
        return $this->getBaseDir() . DS . $this->getAppName() . DS . 'controllers';
    }

    /**
     * Returns path for models directory
     *
     * @return string
     */
    public function getModelsDir() {
        if($this->config->models_dir) {
            return $this->config->models_dir;
        }
        return $this->getBaseDir() . DS . $this->getAppName() . DS . 'models';
    }

    /**
     * Returns path for views directors
     *
     * @return string
     */
    public function getViewsDir() {
        if($this->config->views_dir) {
            return $this->config->views_dir;
        }
        return $this->getBaseDir() . DS . $this->getAppName() . DS . 'views';
    }

    private function __clone() {

    }

    public function route($url, $method, $http_method = 'GET', $secure = false) {
        $route = \Yeah\Fw\Routing\Router::get($url);
        if($route) {
            $route['method'][$http_method] = $method;
            \Yeah\Fw\Routing\Router::add($url, $route);
            return;
        }
        \Yeah\Fw\Routing\Router::add($url, array(
            'route_request_handler' => 'Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler',
            'secure' => $secure,
            'method' => array($http_method => $method),
        ));
    }

    /**
     * Adds new simple route for GET HTTP method
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routeGet($url, $method, $secure = false) {
        $this->route($url, $method, 'GET', $secure);
    }

    /**
     * Adds new simple route for POST HTTP method
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routePost($url, $method, $secure = false) {
        $this->route($url, $method, 'POST', $secure);
    }

    /**
     * Adds new simple route for PUT HTTP method
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routePut($url, $method, $secure = false) {
        $this->route($url, $method, 'PUT', $secure);
    }

    /**
     * Adds new simple route for DELETE HTTP method
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routeDelete($url, $method, $secure = false) {
        $this->route($url, $method, 'DELETE', $secure);
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
     *
     * Configure service factories
     *
     */
    public function configureServices() {
        $dc = $this->getDependencyContainer();

        $dc->set('logger', function() {
            return new \Yeah\Fw\Logger\NullLogger();
        });

        $dc->set('db_config', function() {
            return null;
        });

        $dc->set('session', function() {
            return new \Yeah\Fw\Session\NullSessionHandler();
        });

        $dc->set('auth', function() {
            return new \Yeah\Fw\Auth\NullAuth();
        });

        $dc->set('entity_manager', function() {
            return null;
        });

        $dc->set('view', function() {
            return new \Yeah\Fw\Mvc\PhpView(App::getInstance()->getViewsDir(), array(
                'cache' => $this->getCacheDir()
            ));
        });
    }

    /**
     * Returns current application instance
     *
     * @param mixed $options Application options
     * @return \\Yeah\\Fw\\Application\\App
     */
    public static function getInstance() {
        return static::$instance;
    }

}
