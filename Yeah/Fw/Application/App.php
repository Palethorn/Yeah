<?php

namespace Yeah\Fw\Application;

/**
 * Implements singleton pattern. Used for application request entry point
 * 
 * @property Router $router
 * @property Request $request
 * @property Response $response
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

    /**
     * Class constructor.
     * @param mixed $options Configuration options
     * 
     */
    private function __construct($options = array()) {
        $this->options = $options;
        $this->createInstances();
    }

    /**
     * Creates application instances from application settings.
     */
    private function createInstances() {
        $lib = $this->options['project_paths']['lib'];
        // Register autoloader
        require_once $lib . DS . 'Yeah' . DS . 'Fw' . DS . 'Application' . DS . 'Autoloader.php';
        $autoloader = new Application\Autoloader();
        $autoloader->setIncludePath($lib);
        $autoloader->register();
        
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

    /**
     * Begins chain execution
     */
    public function execute() {
        $ret = $this->executeRouter();
        $ret = $this->executeSecurity($ret);
        $ret = $this->executeAction($ret);
        $this->executeRender($ret);
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
        if($route->isSecure()) {
            $auth = $this->getAuth();
            if(!$auth->isAuthenticated()) {
                $this->getResponse()->setFlash('You are not logged in!')->redirect($this->options['app']['default_login']);
            }
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
     * Getter for session handler object
     * 
     * @return SessionHandlerInterface
     */
    public function getSessionHandler() {
        return $this->session;
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
     * Getter for logger object
     * 
     * @return \Yeah\Fw\Logger\LoggerInterface
     */
    public function getLogger() {
        return $this->logger;
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
