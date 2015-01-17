<?php

namespace Yeah\Fw\Routing\Route;

/**
 * Exposes route class methods
 * 
 * @author David Cavar
 */
interface RouteInterface {

    /**
     * Retrieves requested action
     * @return string
     */
    function getAction();

    /**
     * Sets requested action
     * 
     * @param string $action
     */
    function setAction($action);

    /**
     * Retrieves requested controller
     * 
     * @return \Yeah\Fw\Mvc\Controller
     */
    function getController();

    /**
     * Sets requested controller
     * 
     * @param \Yeah\Fw\Mvc\Controller $controller
     */
    function setController(\Yeah\Fw\Mvc\Controller $controller);

    /**
     * Returns true if route is secure, false if not
     * 
     * @return bool
     */
    function isSecure();

    /**
     * Set to true if you want to check the route for access, false otherwise
     * 
     * @param bool $secure
     */
    function setSecure($secure);

    /**
     * Executes route action
     */
    function execute(\Yeah\Fw\Http\Request $request, \Yeah\Fw\Http\Response $response, \SessionHandlerInterface $session, \Yeah\Fw\Auth\AuthInterface $auth);
}
