<?php

namespace Yeah\Fw\Db;

/**
 * Interface for database adapter initialization class implemetation
 * 
 * @author David Cavar
 */
interface AdapterInterface {

    function init($options);
}
