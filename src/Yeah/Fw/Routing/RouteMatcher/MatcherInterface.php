<?php

namespace Yeah\Fw\Routing\RouteMatcher;

use Yeah\Fw\Http\Request;

interface MatcherInterface {

    function match($options, Request $request);
}
