<?php
namespace Yeah\Fw\Db;

class PdoLoader {

    public function init($options) {
        PdoConnection::configure($options['dsn'], $options['db_user'], $options['db_password']);
        PdoModel::configure($options['db_schema_path']);
    }
}
