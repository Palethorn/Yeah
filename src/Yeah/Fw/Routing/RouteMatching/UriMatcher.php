<?php

namespace Yeah\Fw\Routing\RouteMatching;

class UriMatcher implements MatcherInterface {

    public function match($options, \Yeah\Fw\Http\Request $request) {
        $request_uri = $request->getRequestUri();
        $pattern = $options['pattern'];
        $requirements = isset($options['requirements']) ? $options['requirements'] : array();

        $pattern = preg_replace_callback('/\{:([a-zA-Z0-9_]+)\}/', function($matches) use(&$requirements) {
            if(isset($requirements[$matches[1]])) {
                return '(' . $requirements[$matches[1]] . ')?';
            }
            return '([a-zA-Z0-9_]+)?';
        }, $pattern);

        if(isset($options['prefix'])) {
            $pattern = $options['prefix'] . $pattern;
        }

        $pattern = '#^' . $pattern . '$#';
        $matches = array();
        if(preg_match($pattern, $request_uri, $matches)) {
            if(count($matches) > 1) {
                array_shift($matches);
            } else {
                return array();
            }
            return $matches;
        }
        return false;
    }

}
