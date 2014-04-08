<?php

namespace Yeah\Fw\Db;

/*
 * @property PdoConnection $db_adapter
 */

abstract class PdoModel {

    private static $schema = array();
    private $db_adapter = null;

    public static function configure($schema_path) {
        require_once $schema_path;
        self::$schema = $schema;
    }

    public function __call($method, $args) {
        if(strpos($method, 'findBy') == 0) {
            $field = strtolower(str_replace('findBy', '', $method));
            return $this->findBy($field, $args[0]);
        }
    }

    public function __construct($options = null) {
        $this->db_adapter = new PdoConnection(\Yeah\Fw\Application\Config::get('database'));
        $this->schema = self::$schema[$this->table];
    }

    public static function findBy($field, $arg, $return_as_object = true) {
        $query = "select * from $this->table where " . $field . '=' . $arg;
        try {
            $r = $this->db_adapter->query($query);
            if($return_as_object) {
                return $r->fetch(\PDO::FETCH_CLASS, get_class());
            } else {
                return $r->fetch(\PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            throw new \Exception('Error in SQL query!', 500, null);
        }
    }

    public function findAll($return_as_object = true) {
        $query = "select * from " . $this->table;
        $r = $this->db_adapter->query($query);
        if($return_as_object) {
            return $r->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, get_class($this));
        }
        return $r->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function save() {
        if($this->exists()) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    public function insert() {
        $values = array();
        $columns = array();
        foreach ($this->schema as $property => $options) {
            if(isset($this->$property)) {
                $columns[] = $property;
                $values[] = "'" . $this->$property . "'";
            }
        }
        $query = 'insert into ' . $this->table . '(' . implode(',', $columns) . ') ' . 'values(' . implode(',', $values) . ')';
        $this->db_adapter->query($query);
    }

    public function update() {
        $columns = array();
        foreach ($this->schema as $property => $options) {
            if(isset($this->$property)) {
                $columns[] = $property . '=' . $this->$property;
            }
        }
        $query = 'update ' . $this->table . ' set ' . implode(',', $columns);
        $this->db_adapter->query($query);
    }

    public function exists() {
        if(!isset($this->id)) {
            return false;
        }
        $query = 'select id from ' . $this->table . ' where id=' . $this->id;
        if($this->db_adapter->query($query)->fetch(\PDO::FETCH_ASSOC)) {
            return true;
        }
        return false;
    }

}
