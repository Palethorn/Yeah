<?php
namespace Yeah\Fw\Application;

class Router {

    private static $routes = array();

    public static function add($route, $params = array()) {
        if(!isset(self::$routes[$route])) {
            self::$routes[$route] = $params;
        }
    }

    public static function get($route) {
        if(isset(self::$routes[$route])) {
            return self::$routes[$route];
        }
        return false;
    }

    public static function remove($route) {
        unset(self::$routes[$route]);
    }

    public function handle(\Yeah\Fw\Http\Request $request) {
        $request_uri = $request->getRequestUri();
        $controller = $request->getParameter('controller');
        $action = $request->getParameter('action') . '_action';
        $route = array('controller' => $controller, 'action' => $request->getParameter('action'));
        if(($r = self::get($request_uri))) {
            $c = $r['controller'];
            $request->setParameter('controller', $c);
            if(isset($r['restful'])) {
                if(!isset($r['restful'][$request->getRequestMethod()])) {
                    throw new \Exception('No allowed methods', 405, null);
                }

                $action = $r['restful'][$request->getRequestMethod()];
                $request->setParameter('action', $action);
            } else {
                $action = $r['action'] . '_action';
                $request->setParameter('action', $r['action']);
            }
            $route = $r;
        }
        return $route;
    }
}
