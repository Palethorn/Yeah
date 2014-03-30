<?php

class generate_model {

    public function execute($params) {
        require_once Config::get('data') . DS . 'schema.php';
        if(count($params) == 0) {
            throw new \Exception('Please specify model name', 500, null);
        }
        $model = substr(ucwords($params[0]), 0, -1);
        $fp = fopen(Config::get('models_base') . DS . $model . '.php', 'w');
        $template = '<?php' . PHP_EOL;
        $template .= 'class ' . $model . ' extends PdoModel {' . PHP_EOL;
        $template .= 'protected $schema = ' . var_export($schema[$params[0]], true) . ';' . PHP_EOL;
        $template .= '}' . PHP_EOL;
        fwrite($fp, $template);
        fclose($fp);
    }

}
