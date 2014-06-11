<?php

namespace Yeah\Fw\Routing;

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
     * @param array $options
     * @return string
     */
    function getController();

    /**
     * Sets requested controller
     * 
     * @param string $controller
     */
    function setController($controller);

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
}
