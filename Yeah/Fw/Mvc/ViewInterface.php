<?php

namespace Yeah\Fw\Mvc;

interface ViewInterface {

    function __construct($views_dir);

    /**
     * @return ViewInterface
     */
    function setTemplate($template = false);

    /**
     * @return ViewInterface
     */
    function withLayout($layout);

    /**
     * @return ViewInterface
     */
    function withParams($params);

    /**
     * @return ViewInterface
     */
    function with($key, $value);

    function render();
}
