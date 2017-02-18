<?php

namespace Yeah\Fw\Middleware;

class Slots {
    const PRE_ROUTING = 0;
    const POST_ROUTING = 1;

    const PRE_SECURITY = 2;
    const POST_SECURITY = 3;

    const PRE_CACHE = 4;
    const POST_CACHE = 5;

    const PRE_ACTION = 6;
    const POST_ACTION = 7;

    const PRE_RENDER = 8;
    const POST_RENDER = 9;

    const PRE_REPONSE_CACHE = 10;
    const POST_REPONSE_CACHE = 11;
}
