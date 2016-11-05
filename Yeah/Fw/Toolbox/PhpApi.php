<?php
namespace Yeah\Fw\Toolbox;

class PhpApi
{
    static function strpos($haystack, $needles)
    {
        if (is_array($needles)) {
            foreach ($needles as $str) {
                if (is_array($str)) {
                    $pos = strpos_array($haystack, $str);
                } else {
                    $pos = strpos($haystack, $str);
                }
                if ($pos !== FALSE) {
                    return $pos;
                }
            }
        } else {
            return strpos($haystack, $needles);
        }
        return false;
    }

    /**
     * Generates random string of specified length
     * @param int Return value Length
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