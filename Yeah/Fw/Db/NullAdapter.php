<?php

namespace Yeah\Fw\Db;

/**
 * Doesn't provide \Yeah\Fw\Db\AdapterInterface implementation.
 * Used for applications with no database requirement.
 * 
 * @author David Cavar
 */
class NullAdapter implements \Yeah\Fw\Db\AdapterInterface {

    /**
     * 
     * @param array $options
     */
    public function init($options) {
        
    }

}
