<?php

namespace Yeah\Fw\Application;

use Yeah\Fw\Error\ErrorHandler;
use Yeah\Fw\Event\EventArgs;
use Yeah\Fw\Event\Event;
use Yeah\Fw\Event\EventDispatcher;
use Yeah\Fw\Http\Request;
use Yeah\Fw\Mvc\Closure;
use Yeah\Fw\Mvc\ViewInterface;
use Yeah\Fw\Routing\Route;
use Yeah\Fw\Routing\Route\RouteInterface;
use Yeah\Fw\Routing\Router;

/**
 * Implements singleton pattern. Used for application request entry point
 *
 * @property Yeah\Fw\Application $instance Singleton instance of this class
 * @property Yeah\Fw\Error\ErrorHandler $error_handler
 * @property Yeah\Fw\Application\DependencyContainer $dc
 * @property Yeah\Fw\Application\Config $config
 * @property Route $route
 * @author David Cavar
 */
class App {

    protected static $instance = null;
    protected $error_handler = null;
    protected $dc = null;
    protected $app_name = '';
    protected $config = null;
    protected $env = 'prod';
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

        $conf = array();

        if(isset($config[$env])) {
            $conf = $config[$env];
        } else {
            $conf = $config;
        }

        $this->config = new Config($conf);

        if(PHP_MAJOR_VERSION == 7) {
            $this->error_handler = new ErrorHandler(error_reporting());
        }

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
     * Each job has a pre and post event
     */
    public function execute() {
        $this->getEventDispatcher()->dispatch(new Event(Event::PRE_ROUTING, $this, new EventArgs()));
        $this->executeRouter();
        $this->getEventDispatcher()->dispatch(new Event(Event::POST_ROUTING, $this, new EventArgs()));
        $this->getEventDispatcher()->dispatch(new Event(Event::PRE_SECURITY, $this, new EventArgs()));
        $this->executeSecurity();
        $this->getEventDispatcher()->dispatch(new Event(Event::POST_SECURITY, $this, new EventArgs()));
        $this->getEventDispatcher()->dispatch(new Event(Event::PRE_CACHE, $this, new EventArgs()));

        if($this->route->getIsCacheable() && $this->executeCache($this->route)) {
            return;
        }

        $this->getEventDispatcher()->dispatch(new Event(Event::POST_CACHE, $this, new EventArgs()));
        $this->getEventDispatcher()->dispatch(new Event(Event::PRE_ACTION, $this, new EventArgs()));
        $response = $this->executeAction($this->route);
        $this->getEventDispatcher()->dispatch(new Event(Event::POST_ACTION, $this, new EventArgs()));
        $response->write();
    }

    /**
     * Performs matching the URI to the the route inside of a chain execution.
     * @return RouteInterface
     */
    private function executeRouter() {
        return $this->route = $this->getRouter()->handle($this->getRequest());
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
     * @param RouteInterface $route
     * @return View Controller view object
     */
    private function executeAction(Route $route) {
        $controller_class = $route->getController();
        $action = $route->getAction();
        $controller = new $controller_class($action);

        $reflectionClass = new \ReflectionClass($controller_class);
        $methods = $reflectionClass->getMethods();
        
        foreach($methods as $method) {
            $matches = array();

            if(preg_match('/^set(.*)$/', $method->name, $matches)) {
                $id = trim(preg_replace_callback('/[A-Z]/', function($matches) {
                    return strtolower('_' . $matches[0]);
                }, $matches[1]), '_');
                
                if($this->dc->has($id)) {
                    $controller->{$method->name}($this->dc->get($id));
                }
            }
        }

        return $controller->call();
    }

    /**
     * Write cached output to response if route is cacheable and cache exists.
     *
     * @param Yeah\Fw\Routing\Route\RouteInterface $route
     * @return boolean
     */
    private function executeCache(Route $route) {
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
     * @param $view Controller view object
     */
    private function executeRender($response) {
        if($response instanceof ViewInterface) {
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

        $this->getEventDispatcher()->dispatch(new Event(Event::PRE_REPONSE_CACHE, $this, new EventArgs()));

        if($this->route->getIsCacheable()) {
            $this->getResponseCache()->set($this->getUrlCacheKey(), $output, $this->route->getCacheDuration());
        }

        $this->getEventDispatcher()->dispatch(new Event(Event::POST_REPONSE_CACHE, $this, new EventArgs()));
    }

    /**
     * Getter for HTTP request object. Retrieves request object which represents abstracted version og HTTP request.
     *
     * @return \Yeah\Fw\Http\Request
     */
    public function getRequest() {
        return $this->dc->get('request');
    }

    /**
     * Getter for router object.
     *
     * @return RouterInterface
     */
    public function getRouter() {
        return $this->dc->get('router');
    }

    /**
     * Getter for event dispatcher object.
     *
     * @return EventDispatcher
     */
    public function getEventDispatcher() {
        return $this->dc->get('event_dispatcher');
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
     * @return CacheInterface
     */
    public function getResponseCache() {
        return $this->dc->get('response_cache');
    }

    /**
     * Retrieves view renderer from service container.
     *
     * @return ViewInterface
     */
    public function getView() {
        return $this->getDependencyContainer()->get('view');
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
    public function route($url, \Closure $closure, $http_method = 'GET', $secure = false, $cache_options = array('is_cacheable' => false, 'cache_duration' => 1440)) {
        $route = $this->getRouter()->get($url);

        if($route) {
            $route['restful'][$http_method] = array(
                'controller' => Closure::class,
                'action' => $closure,
                'cache' => $cache_options,
                'secure' => $secure
            );

            $this->getRouter()->add($url, $route);
            return;
        }
        
        $this->getRouter()->add($url, array(
            'secure' => $secure,
            'restful' => array(
                $http_method => array(
                    'controller' => Closure::class,
                    'action' => $closure,
                    'cache' => $cache_options,
                    'secure' => $secure
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
    public function routeGet($url, \Closure $closure, $secure = false, $cache_options = array('is_cacheable' => false, 'cache_duration' => 1440)) {
        $this->route($url, $closure, 'GET', $secure, $cache_options);
    }

    /**
     * Adds new simple route for POST HTTP method.
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routePost($url, \Closure $closure, $secure = false) {
        $this->route($url, $closure, 'POST', $secure);
    }

    /**
     * Adds new simple route for PUT HTTP method.
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routePut($url, \Closure $closure, $secure = false) {
        $this->route($url, $closure, 'PUT', $secure);
    }

    /**
     * Adds new simple route for DELETE HTTP method.
     *
     * @param string $url
     * @param string $method
     * @param bool $secure
     */
    public function routeDelete($url, \Closure $closure, $secure = false) {
        $this->route($url, $closure, 'DELETE', $secure);
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
        $this->dc = new DependencyContainer();

        $this->dc->set('router', array(
            'class' => Router::class
        ));

        $this->dc->set('request', array(
            'class' => Request::class
        ));

        $this->dc->set('event_dispatcher', array(
            'class' => EventDispatcher::class
        ));

        $this->dc->set('logger', array(
            'class' => 'Yeah\Fw\Logger\NullLogger'
        ));

        $this->dc->set('session', array(
            'class' => 'Yeah\Fw\Session\NullSessionHandler'
        ));

        $this->dc->set('auth', array(
            'class' => 'Yeah\Fw\Auth\NullAuth'
        ));

        $this->dc->set('view', array(
            'class' => 'Yeah\Fw\Mvc\PhpView',
            'params' => array(
                $this->getViewsDir(),
                array(
                    'cache' => $this->getCacheDir()
                ))
        ));

        $this->dc->set('response_cache', array(
            'class' => 'Yeah\Fw\Cache\FileCache',
            'params' => array(
                $this->getCacheDir(),
                3600
            )
        ));
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
