<?php
namespace Yeah\Fw\Db;

abstract class PdoModel {
    private static $schema = array();
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

    public function __construct() {
        $this->dbAdapter = PdoConnection::getInstance();
        $this->table = strtolower(get_class($this)) . 's';
        $this->schema = self::$schema[$this->table];
    }

    public function findBy($field, $arg) {
        $query = "select * from $this->table where " . $field . '=:' . $field;
        try {
            $stmt = $this->dbAdapter->prepare($query);
            $stmt->bindParam(':' . $field, $arg, $this->schema[$field]['pdo_type']);
            $stmt->execute();
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);
            if(count($res) > 0) {
                return $res;
            } else {
                return false;
            }
        } catch(Exception $e) {
            throw new \Exception('Error in SQL query!', 500, null);
        }
    }

    public function findAll() {
        $query = "select * from " . $this->table;
        $stmt = $this->dbAdapter->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function createFromArray($params) {
        $q = 'insert into ' . $this->table . '(';
        $v = ' values(';
        foreach($this->schema as $field => $info) {
            if(isset($params[$field])) {
                $q .= $field . ',';
                $v .= '?,';
                $values[] = $params[$field];
            }
        }
        $q = substr($q, 0, -1) . ')';
        $v = substr($v, 0, -1) . ')';
        $q .= $v;
        $db = PdoConnection::getInstance();
        $db->beginTransaction();
        $stmt = $db->prepare($q);
        try {
            $stmt->execute($values);
            $id = $db->lastInsertId();
            $db->commit();
            return $id;
        } catch(PdoException $e) {
            throw new \Exception($e->getMessage(), 500, null);
        }
    }
    
        public function updateFromArray($params, $where = false) {
        $q = 'update ' . $this->table . ' set ';
        foreach($this->schema as $field => $info) {
            if(isset($params[$field])) {
                $q .= $field . '=?,';
                $values[] = $params[$field];
            }
        }
        $q = substr($q, 0, -1);
        if($where) {
            $q .= ' where ' . $where;
        } else {
            $q .= ' where id=' . $params['id'];
        }
        $db = PdoConnection::getInstance();
        $db->beginTransaction();
        $stmt = $db->prepare($q);
        try {
            $stmt->execute($values);
            $db->commit();
            return true;
        } catch(PdoException $e) {
            throw new \Exception($e->getMessage(), 500, null);
        }
    }
}
