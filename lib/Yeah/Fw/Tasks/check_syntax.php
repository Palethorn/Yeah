<?php

class check_syntax {
    public function execute($params) {
        exec('find ' . $params[0] . ' -type f -name \*.php -exec php -l {} \;', $output, $return);
        foreach($output as $line) {
            echo $line . PHP_EOL;
        }
    }
}