<?php

namespace Yeah\Fw\Mvc;

interface ViewInterface {

    function __construct($views_dir);

    function setTemplate($template = false);

    function withLayout($layout);

    function withParams($params);

    function with($key, $value);

    function render();
}
