<?php

namespace Yeah\Fw\Mvc;

interface ViewInterface {

    function __construct($views_dir);

    /**
     * Set template
     * 
     * @return ViewInterface
     */
    function setTemplate($template = false);

    /**
     * Set layout
     * 
     * @return ViewInterface
     */
    function withLayout($layout);

    /**
     * Inject array of parameters to view scope
     * 
     * @return ViewInterface
     */
    function withParams($params);

    /**
     * Inject parameter to view scope
     * 
     * @return ViewInterface
     */
    function with($key, $value);

    /**
     * Renders and returns rendered data
     * 
     * @return string
     */
    function render();
}
