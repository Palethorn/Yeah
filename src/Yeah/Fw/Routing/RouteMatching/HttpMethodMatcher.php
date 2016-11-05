<?php

namespace Yeah\Fw\Routing\RouteMatching;

class HttpMethodMatcher implements \Yeah\Fw\Routing\RouteMatching\MatcherInterface {

    public function match($options, \Yeah\Fw\Http\Request $request) {
        $method = $request->getRequestMethod();
        if(!isset($options['restful'][$method])) {
            return false;
        }
        return true;
    }

}
