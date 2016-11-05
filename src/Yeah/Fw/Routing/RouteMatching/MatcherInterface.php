<?php

namespace Yeah\Fw\Routing\RouteMatching;

interface MatcherInterface {

    function match($options, \Yeah\Fw\Http\Request $request);
}
