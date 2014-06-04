<?php
namespace Yeah\Fw\Toolbox;

class Various {
    /**
     * Generates random string of specified length
     * @param int length
     */
    public static function generateRandomString($length) {
        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUWXYZ';
        $pool = str_shuffle($pool);
        $str = '';
        for($i = 0; $i < $length; $i++) {
            $pos = rand(0, strlen($pool) - 1);
            $str .= $pool[$pos];
        }
        return $str;
    }
    
}
