<?php

namespace Yeah\Fw\Application;

use Yeah\Fw\Event\EventArgs;
use Yeah\Fw\Event\Event;
use Yeah\Fw\Event\EventDispatcher;

/**
 * Implements singleton pattern. Used for application request entry point
 *
 * @property Yeah\Fw\Application $instance Singleton instance of this class
 * @property Yeah\Fw\Error\ErrorHandler $error_handler
 * @property Yeah\Fw\Logger\LoggerInterface $logger
 * @property SessionHandlerInterface $session
 * @property Yeah\Fw\Auth\AuthInterface $auth
 * @property Yeah\Fw\Mvc\View\ViewInterface $view
 * @property Yeah\Fw\Application\DependencyContainer $dc
 * @property Yeah\Fw\Routing\Route\Router $router
 * @property Request $request
 * @property Response $response
 * @property Yeah\Fw\Application\Autoloader $autoloader
 * @property Yeah\Fw\Application\Config $config
 * @property \Yeah\Fw\Routing\Route\RouteInterface $route
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
    protected $env = false;
    protected $response_cache = false;
    protected $route = false;
    protected $response_cache_key = false;

    /**
     * Class default constructor
     * @param string $app_name Besides used in some friendly messages and decoration, it is also used for deducing
     * default paths
     * @param string $env Represents a section of your config which identifies development, testing, local, production or
     * whatever other you choose to load. See palethorn.github.io/yeah/tutorial#environments
     * @param mixed $config Array of different config sections which are also arrays representing various configuration
     * options. See See palethorn.github.io/yeah/tutorial#config
     *
     */
    public function __construct($app_name, $env = 'prod', $config = array('prod' => array())) {
        header('X-Powered-By: Yeah Web Framework');
        $this->app_name = $app_name;
        $this->env = $env;
        require_once 'Config.php';
        $conf = array();

        if(isset($config[$env])) {
            $conf = $config[$env];
        }

        $this->config = new Config($conf);

        if(PHP_MAJOR_VERSION == 7) {
            $this->error_handler = new \Yeah\Fw\Error\ErrorHandler_php7(error_reporting());
        } else {
            $this->error_handler = new \Yeah\Fw\Error\ErrorHandler(error_reporting());
        }

        $this->router = new \Yeah\Fw\Routing\Router();
        $this->request = new \Yeah\Fw\Http\Request();
        $this->response = new \Yeah\Fw\Http\Response();
        $this->dc = new DependencyContainer();
        $this->event_dispatcher = new EventDispatcher();
        $this->configureServices();
        $this->loadRoutes();
        self::$instance = $this;
    }

    /**
     * Getter for $app_name property. Returns the application name passed to the constructor
     * on the application initialization
     * @return string
     */
    public function getAppName() {
        return $this->app_name;
    }

    /**
     * Getter for environment property. Returns the application environment passed to the consturctor
     * on the application initialization
     */
    public function getEnvironment() {
        return $this->env;
    }

    /**
     * Register autoloader paths for probing. Method is marked for override
     * @deprecated All autoloading is handled by composer autoload functionality
     */
    protected function registerAutoloaders() {
        require_once $this->getLibDir() . DIRECTORY_SEPARATOR . 'Yeah' . DIRECTORY_SEPARATOR . 'Fw' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Autoloader.php';
        $this->autoloader = new Autoloader();
        $this->autoloader->addIncludePath($this->getLibDir());
        $this->autoloader->addIncludePath($this->getModelsDir());
        $this->autoloader->addIncludePath($this->getControllersDir());
        $this->autoloader->register();
    }

    /**
     * Method used for configuring autoload cache strategy. Method is marked for override
     * @deprecated All autoloading is handled by composer autoload functionality
     */
    public function configureAutoloadCache() {
        $this->autoloader->setCache(new \Yeah\Fw\Cache\NullCache());
    }

    /**
     * Load additional routes. Method is marked for override.
     */
    public function loadRoutes() {
        $routes_location = $this->getBaseDir() .
        DIRECTORY_SEPARATOR .
        $this->getAppName() .
        DIRECTORY_SEPARATOR .
        'config' .
        DIRECTORY_SEPARATOR .
        'routes.php';

        if(file_exists($routes_location)) {
            require_once $routes_location;
        }
    }

    /**
     * Begins chain execution. Executes each separate job for request, and their respective event handlers.
     *
     * Current list of jobs:
     * - Routing
     * - Security
     * - Route response cache
     * - Matching controller action execution
     * - Output rendering
     *
     * Each job has a pre and post event dispatcher
     */
    public function execute() {
        $this->event_dispatcher->dispatch(new Event(Event::PRE_ROUTING, $this, new EventArgs()));
        $this->executeRouter();
        $this->event_dispatcher->dispatch(new Event(Event::POST_ROUTING, $this, new EventArgs()));
        $this->event_dispatcher->dispatch(new Event(Event::PRE_SECURITY, $this, new EventArgs()));
        $this->executeSecurity();
        $this->event_dispatcher->dispatch(new Event(Event::POST_SECURITY, $this, new EventArgs()));
        $this->event_dispatcher->dispatch(new Event(Event::PRE_CACHE, $this, new EventArgs()));

        if($this->route->getIsCacheable() && $this->executeCache($this->route)) {
            return;
        }

        $this->event_dispatcher->dispatch(new Event(Event::POST_CACHE, $this, new EventArgs()));
        $this->event_dispatcher->dispatch(new Event(Event::PRE_ACTION, $this, new EventArgs()));
        $action_result = $this->executeAction($this->route);
        $this->event_dispatcher->dispatch(new Event(Event::POST_ACTION, $this, new EventArgs()));
        $this->event_dispatcher->dispatch(new Event(Event::PRE_RENDER, $this, new EventArgs()));
        $this->executeRender($action_result);
        $this->event_dispatcher->dispatch(new Event(Event::POST_RENDER, $this, new EventArgs()));
    }

    /**
     * Performs matching the URI to the the route inside of a chain execution.
     * @return RouteInterface
     */
    private function executeRouter() {
        return $this->route = $this->router->handle($this->request);
    }

    /**
     * Executes access checkup inside chain execution. Can be used for authentication, authorization and generally for
     * various access controlls. Just implement \\Yeah\\Fw\\Auth\\AuthInterface,
     * and override \\Yeah\\Fw\\Application\\App::configureServices for configuration
     *
     * @param RouteInterface $route Route object
     * @return RouteInterface
     */
    private function executeSecurity() {
        if($this->route->isSecure() && (!$this->getAuth()->isAuthenticated() || !$this->getAuth()->isAuthorized($this->route))) {
            throw new \Yeah\Fw\Http\Exception\UnauthorizedHttpException();
        }
    }

    /**
     * Executes action inside chain execution. Executes controller action from inside the route.
     *
     * @param mixed $route Route options
     * @return Yeah\Fw\Mvc\View Controller view object
     */
    private function executeAction(\Yeah\Fw\Routing\Route\RouteInterface $route) {
        return $route->execute(
                        $this->getRequest(), $this->getResponse(), $this->getSession(), $this->getAuth()
        );
    }

    /**
     * Write cached output to response if route is cacheable and cache exists.
     *
     * @param Yeah\Fw\Routing\Route\RouteInterface $route
     * @return boolean
     */
    private function executeCache(\Yeah\Fw\Routing\Route\RouteInterface $route) {
        if(!$route->getIsCacheable()) {
            return false;
        }

        $response_cache = $this->getResponseCache();

        if($response_cache->has($this->getUrlCacheKey())) {
            $this->response->write($response_cache->get($this->getUrlCacheKey()));
            return true;
        }

        return false;
    }

    /**
     * Executes view rendering inside chain execution. Supports various templating engines through adapter
     * implementation. Twig work out of the box.
     *
     * @param mixed|\\Yeah\\Fw\\Mvc\\ViewInterface $view Controller view object
     */
    private function executeRender($response) {
        if($response instanceof \Yeah\Fw\Mvc\ViewInterface) {
            $output = $response->render();
            $this->response->write($output);
        }

        if(is_array($response)) {
            $layout = isset($response['layout']) ? $response['layout'] : 'default';
            $template = isset($response['template']) ? $response['template'] : ($this->request->getParameter('action') ? $this->request->getParameter('action') : 'index');
            $output = $this->getView()
                    ->setTemplate($template)
                    ->withLayout($layout)
                    ->withParams($response)
                    ->render();
            $this->response->write($output);
        }

        $this->executeMiddleware(\Yeah\Fw\Middleware\Slots::PRE_REPONSE_CACHE);

        if($this->route->getIsCacheable()) {
            $this->getResponseCache()->set($this->getUrlCacheKey(), $output, $this->route->getCacheDuration());
        }

        $this->executeMiddleware(\Yeah\Fw\Middleware\Slots::POST_REPONSE_CACHE);
    }

    /**
     * Getter for HTTP request object. Retrieves request object which represents abstracted version og HTTP request.
     *
     * @return \Yeah\Fw\Http\Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Getter for HTTP response object.
     *
     * @return \Yeah\Fw\Http\Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Getter for router object.
     *
     * @return \\Yeah\\Fw\\Routing\\Router
     */
    public function getRouter() {
        return $this->router;
    }

    /**
     * Retrieves database config from service container. Can also be used to directly configure the neccessary
     * ORM or DBAL
     * @deprecated Database configuration doesn't requre developer overriding service configuration for this.
     */
    public function getDatabaseConfig() {
        return $this->getDependencyContainer()->get('db_config');
    }

    /**
     * Retrieves session handler object from object container.
     *
     * @return \SessionHandlerInterface
     */
    public function getSession() {
        return $this->getDependencyContainer()->get('session');
    }

    /**
     * Retrieves authorization, and authentication object from service container.
     * @return \Yeah\Fw\Auth\AuthInterface
     */
    public function getAuth() {
        return $this->getDependencyContainer()->get('auth');
    }

    /**
     * Retrieves logger object from service container.
     *
     * @return \\Yeah\\Fw\\Logger\\LoggerInterface
     */
    public function getLogger() {
        return $this->getDependencyContainer()->get('logger');
    }

    /**
     * Retrieves response cache handler from service container.
     *
     * @return Yeah\\Fw\\Cache\\CacheInterface
     */
    public function getResponseCache() {
        return $this->dc->get('response_cache');
    }

    /**
     * Retrieves view renderer from service container.
     *
     * @return \\Yeah\\Fw\\Mvc\\ViewInterface
     */
    public function getView() {
        return $this->getDependencyContainer()->get('view');
    }

    /**
     * Get application autoloader
     * @param \\Yeah\\Fw\\Application\\Autoloader $autoloader

     * @deprecated Autoloading is handled by composer autoload functionality
     */
    public function getAutoloader() {
        return $this->autoloader;
    }

    /**
     * Returns path for application root dir. If the base_dir is not passed through config then the method tries
     * to guess most probable path.
     *
     * @return string
     */
    public function getBaseDir() {
        if($this->config->base_dir) {
            return $this->config->base_dir;
        }
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
    }

    /**
     * Returns path for library directory. If the lib_dir is not passed through config then the method tries
     * to guess most probable path.
     *
     * @return string
     */
    public function getLibDir() {
        if($this->config->lib_dir) {
            return $this->config->lib_dir;
        }

        return $this->getBaseDir() . DIRECTORY_SEPARATOR . 'lib';
    }

    /**
     * Returns path for public directory. If the web_dir is not passed through config then the method tries
     * to guess most probable path.
     *
     * @return string
     */
    public function getWebDir() {
        if($this->config->web_dir) {
            return $this->config->web_dir;
        }
        return $this->getBaseDir() . DIRECTORY_SEPARATOR . 'web';
    }

    /**
     * Return path for cache directory. If the cache_dir is not passed through config then the method tries
     * to guess most probable path.
     *
     * @return string
     */
    public function getCacheDir() {
        if($this->config->cache_dir) {
            return $this->config->cache_dir;
        }

        return $this->getBaseDir() . DIRECTORY_SEPARATOR . 'cache';
    }

    /**
     * Returns path for logs directory. If the log_dir is not passed through config then the method tries
     * to guess most probable path.
     *
     * @return string
     */
    public function getLogDir() {
        if($this->config->log_dir) {
            return $this->config->log_dir;
        }

        return $this->getBaseDir() . DIRECTORY_SEPARATOR . 'log';
    }

    /**
     * Returns path for controllers directory. If the controllers_dir is not passed through config then the method tries
     * to guess most probable path.
     *
     * @return string
     */
    public function getControllersDir() {
        if($this->config->controllers_dir) {
            return $this->config->controllers_dir;
        }

        return $this->getBaseDir() . DIRECTORY_SEPARATOR . $this->getAppName() . DIRECTORY_SEPARATOR . 'controllers';
    }

    /**
     * Returns path for models directory. If the models_dir is not passed through config then the method tries
     * to guess most probable path.
     *
     * @return string
     */
    public function getModelsDir() {
        if($this->config->models_dir) {
            return $this->config->models_dir;
        }

        return $this->getBaseDir() . DIRECTORY_SEPARATOR . $this->getAppName() . DIRECTORY_SEPARATOR . 'models';
    }

    /**
     * Returns path for views directors. If the views_dir is not passed through config then the method tries
     * to guess most probable path.
     *
     * @return string
     */
    public function getViewsDir() {
        if($this->config->views_dir) {
            return $this->config->views_dir;
        }

        return $this->getBaseDir() . DIRECTORY_SEPARATOR . $this->getAppName() . DIRECTORY_SEPARATOR . 'views';
    }

    private function __clone() {

    }

    /**
     * General method used for appending simple routes
     *
     * @param string $url
     * @param Closure $method
     * @param string $http_method
     * @param bool $secure
     */
    public function route($url, $method, $http_method = 'GET', $secure = false, $cache_options = array('is_cacheable' => false, 'cache_duration' => 1440)) {
        $route = \Yeah\Fw\Routing\Router::get($url);
        
        if($route) {
            $route['restful'][$http_method] = array(
                'method' => $method,
                'cache' => $cache_options,
                'secure' => $secure
            );
            \Yeah\Fw\Routing\Router::add($url, $route);
            return;
        }

        \Yeah\Fw\Routing\Router::add($url, array(
            'route_request_handler' => 'Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler',
            'secure' => $secure,
            'restful' =>
            array($http_method => (
                array(
                    'method' => $method,
                    'cache' => $cache_options,
                    'secure' => $secure
                )
                )
            )
        ));
    }

    /**
     * Adds new simple route for GET HTTP method.
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routeGet($url, $method, $secure = false, $cache_options = array('is_cacheable' => false, 'cache_duration' => 1440)) {
        $this->route($url, $method, 'GET', $secure, $cache_options);
    }

    /**
     * Adds new simple route for POST HTTP method.
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routePost($url, $method, $secure = false) {
        $this->route($url, $method, 'POST', $secure);
    }

    /**
     * Adds new simple route for PUT HTTP method.
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routePut($url, $method, $secure = false) {
        $this->route($url, $method, 'PUT', $secure);
    }

    /**
     * Adds new simple route for DELETE HTTP method.
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routeDelete($url, $method, $secure = false) {
        $this->route($url, $method, 'DELETE', $secure);
    }

    /**
     * Returns dependency container object.
     *
     * @return DependencyContainer
     */
    public function getDependencyContainer() {
        return $this->dc;
    }

    /**
     *
     * Configure service factories. Method marked for override. You can populate service container here.
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

        $dc->set('response_cache', function () {
            return \Yeah\Fw\Cache\CacheFactory::create();
        });
    }

    /**
     * Returns current application instance
     *
     * @return \\Yeah\\Fw\\Application\\App
     */
    public static function getInstance() {
        return static::$instance;
    }

    /**
    * Override this method if you need some other format of response cache keys.
    */
    public function getUrlCacheKey() {
        if($this->response_cache_key) {
            return $this->response_cache_key;
        }
        return $this->response_cache_key = str_replace(array('/', '?', '&'), array('_', '_', '_'), $this->getRequest()->getHttpHost() . $this->getRequest()->getEnvironmentParameter('REQUEST_URI') . $this->getRequest()->getQueryString());
    }

    public function getConfig() {
        return $this->config;
    }

}
