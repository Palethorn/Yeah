<?php
namespace Yeah\Fw\Http\Exception;

/*
 * 
 */

interface HttpExceptionInterface {
    /**
     * Return HTTP status code
     * @return int
     */
    function getStatusCode();
}