<?php

namespace Yeah\Fw\Toolbox;

class Debug {

    public static function dd($data) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die();
    }

}
