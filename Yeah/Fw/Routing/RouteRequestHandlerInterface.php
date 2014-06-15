<?php

namespace Yeah\Fw\Routing;

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
    function handle($options, $request);
}
