<?php

namespace Yeah\Fw\Db;

/**
 * Provides abstract implementation of database model class
 */
abstract class PdoModel {

    private static $schema = array();
    private $db_adapter = null;

    /**
     * Configures database ORM model
     * 
     * @param string $schema_path Path for database schema config
     */
    public static function configure($schema_path) {
        require_once $schema_path;
        self::$schema = $schema;
    }

    /**
     * 
     * @param string $method Method name
     * @param mixed $args Method arguments
     * @return Yeah\Fw\Db\PdoModel
     */
    public function __call($method, $args) {
        if(strpos($method, 'findBy') == 0) {
            $field = strtolower(str_replace('findBy', '', $method));
            return $this->findBy($field, $args[0]);
        }
    }

    /**
     * Creates new PdoModel instance
     * 
     * @param mixed $options PdoModel options
     */
    public function __construct($options = null) {
        $this->db_adapter = new PdoConnection(\Yeah\Fw\Application\Config::get('database'));
        $this->schema = self::$schema[$this->table];
    }
    
    /**
     * Finds record by specified table field and value
     * 
     * @param string $field Table field
     * @param string $arg Field value
     * @param boolean $return_as_object Should the result be bound to model or
     * returned as array
     * @return mixed
     * @throws \Exception
     */
    public function findBy($field, $arg, $return_as_object = true) {
        if($field != 'id') {
            $arg = '\'' . $arg . '\'';
        }
        $query = "select * from " . $this->table . " where " . $field . '=' . $arg . ' limit 1';
        try {
            $r = $this->db_adapter->query($query);
            if($return_as_object) {
                $r->setFetchMode(\PDO::FETCH_INTO, $this);
                return $r->fetch();
            } else {
                return $r->fetch(\PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            throw new \Exception('Error in SQL query!', 500, null);
        }
    }

    /**
     * Finds all records from table
     * 
     * @param bool $return_as_object Should the result be fetched as object or
     * plain array
     * @return mixed
     * @throws \Exception
     */
    public function findAll($return_as_object = true) {
        $query = "select * from " . $this->table;
        try {
            $r = $this->db_adapter->query($query);
            if($return_as_object) {
                return $r->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, get_class($this));
            }
            return $r->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new \Exception('Error in SQL query!', 500, null);
        }
    }

    /**
     * Saves object properties to database
     */
    public function save() {
        if($this->exists()) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    /**
     * Inserts new row into database
     */
    private function insert() {
        $values = array();
        $columns = array();
        foreach ($this->schema as $property => $options) {
            if(isset($this->$property)) {
                $columns[] = $property;
                if($options['pdo_type'] == \PDO::PARAM_INT) {
                    $values[] = $this->$property;
                } else {
                    $values[] = "'" .  $this->$property . "'";
                }
            }
        }
        $query = 'insert into ' . $this->table . '(' . implode(',', $columns) . ') ' . 'values(' . implode(',', $values) . ')';
        $this->db_adapter->query($query);
        $this->id = $this->db_adapter->lastInsertId();
    }

    /**
     * Updates existing row in database
     */
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

    /**
     * Checks if a record already exists
     * 
     * @return boolean
     */
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
