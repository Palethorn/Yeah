<?php

namespace Yeah\Fw\Routing;

/**
 * Exposes route request class methods
 * 
 * @author David Cavar
 */
interface RouteRequestInterface {
    /**
     * Generates route object from request
     * 
     * @param array $options
     * @return RouteInterface
     */
    function handle($options);
}
