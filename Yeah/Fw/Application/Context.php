<?php
namespace Yeah\Fw\Application;
/**
 * @property Request $request Description
 * @property Response $response Description
 * @property Router $router Desc
 * @property SessionHandlerInterface $session Description
 * @property Logger $logger Description
 */
class Context {

    private static $instance = null;
    private $request = null;
    private $response = null;
    private $router = null;
    private $session = null;
    private $logger = null;
    private $controllers_path = null;

    private function __construct($options = array()) {
        $this->controllers_path = $options['controllers_path'];
        $this->createInstances($options);
    }

    public function createInstances($options) {
        $this->logger = new \Yeah\Fw\Logger\FileLogger($options['log_path'] . DS . 'general.log');
        $this->session = new \Yeah\Fw\Session\DatabaseSessionHandler($options['database']);
        $this->request = new \Yeah\Fw\Http\Request();
        $this->response = new \Yeah\Fw\Http\Response();
        $this->router = new \Yeah\Fw\Application\Router();
        $this->auth = new \Yeah\Fw\Aaa\Auth($this->session);
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
        if (isset($route['secure']) && $route['secure'] == true) {
            $auth = $this->getAuth();
            if (!$auth->isAuthenticated()) {
                $this->getResponse()->setFlash('You are not logged in!')->redirect('/user/login');
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
        $this->controller = new $class($this->request, $this->response, $this->getSessionHandler(), $this->getLogger());

        if (!method_exists($this->controller, $method) || !is_callable($class . '::' . $method)) {
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
        if ($this->getRequest()->getContentType() == 'application/json') {
            $this->response->writeJson($view->render());
        } else if ($view != null) {
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
        if (!isset(static::$instance)) {
            static::$instance = new Context($options);
        }
        return static::$instance;
    }

}
