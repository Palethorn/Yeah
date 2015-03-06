<?php

namespace Yeah\Fw\Routing\RouteRequest;

/**
 * Exposes route request class methods
 * 
 * @author David Cavar
 */
interface RouteRequestHandlerInterface {

    /**
     * Checks match contition and generates route object from request
     * 
     * @param array $options
     * @param Yeah\Fw\Http\Request $request
     * @return RouteInterface
     */
    function handle($options, \Yeah\Fw\Http\Request $request);

    /**
     * Tries to match requested uri with route pattern
     * 
     * @param string $uri
     * @param string $pattern
     */
    function match($uri, $pattern);
    
    function configureCache(\Yeah\Fw\Routing\Route\RouteInterface $route, $options);
}
