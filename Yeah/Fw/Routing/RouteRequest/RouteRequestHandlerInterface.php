<?php

namespace Yeah\Fw\Routing\RouteRequest;

/**
 * Exposes route request class methods
 * 
 * @author David Cavar
 */
interface RouteRequestHandlerInterface {
    /**
     * Generates route object from request
     * 
     * @param array $options
     * @param Yeah\Fw\Http\Request $request
     * @return RouteInterface
     */
    function handle($options, \Yeah\Fw\Http\Request $request);
    
    function match($uri, $pattern);
}
