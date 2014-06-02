<?php
namespace Yeah\Fw\Db;

/**
 * Interface for database adapter initialization class implemetation
 */
interface AdapterInterface {
    function init($options);
}
