<?php

namespace Yeah\Fw\Routing\RouteMatcher;

use Yeah\Fw\Http\Request;

class HttpMethodMatcher implements \Yeah\Fw\Routing\RouteMatcher\MatcherInterface {

    public function match($options, Request $request) {
        $method = $request->getRequestMethod();

        if(!isset($options['restful'][$method])) {
            return false;
        }
        
        return true;
    }

}
