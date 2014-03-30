<?php

class generate_schema {

    public function execute($params) {
        $db = new PDO('mysql:host=' . Config::get('db_server') . ';dbname=' . Config::get('db_database') . ';charset=utf8', Config::get('db_user'), Config::get('db_password'));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare('SHOW TABLES');
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $fp = fopen(Config::get('data') . DS . 'schema.php', 'w');
        $schema = array();
        foreach ($tables as $t) {
            $table = $t['Tables_in_' . Config::get('db_database')];
            $stmt = $db->prepare('DESCRIBE ' . $table);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $fields = array();
            foreach ($result as $field) {
                $field['pdo_type'] = $this->detectType($field['Type']);
                $fields[$field['Field']] = $field;
            }
            $schema[$table] = $fields;
        }
        $s = '$schema = ' . var_export($schema, true) . ';';
        fwrite($fp, '<?php' . PHP_EOL);
        fwrite($fp, $s);
        fclose($fp);
    }

    function detectType($type) {
        if (strpos($type, 'int') === 0) {
            return PDO::PARAM_INT;
        }
        if(strpos($type, 'tinyint') === 0) {
            return PDO::PARAM_BOOL;
        }
        return PDO::PARAM_STR;
    }

}
