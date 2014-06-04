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
        var_dump($data);
        echo '</pre>';
        die();
    }

}
