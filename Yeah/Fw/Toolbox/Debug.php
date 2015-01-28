<?php

namespace Yeah\Fw\Toolbox;

class Debug {

    /**
     * Dumps data and dies
     * 
     * @param mixed $data
     */
    public static function dd($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die();
    }

}
