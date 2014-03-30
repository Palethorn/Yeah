<?php
namespace Yeah\Fw\Db;

class PdoQuery {

    private $parts = array();

    public function Select($fields) {
        $this->parts[0] = 'select ' . $fields;
        return $this;
    }

    public function From($tables) {
        $this->parts[1] = ' from ' . $tables;
        return $this;
    }

    public function InnerJoin($table, $condition) {
        $this->parts[2][] = ' inner join ' . $table . ' on ' . $condition;
        return $this;
    }

    public function Where($field, $param = null) {
        $part = ' where ' . $field;
        if(isset($param)) {
            if(is_array($param)) {
                $part .= ' in (' . implode(',', $param) . ')';
            } else {
                $part .= '=' . $param;
            }
        }
        $this->parts[3][] = $part;
        return $this;
    }

    public function AndWhere($field, $param) {
        $part = ' and ' . $field;
        if(isset($param)) {
            if(is_array($param)) {
                $part .= ' in (' . implode(',', $param) . ')';
            } else {
                $part .= '=' . $param;
            }
        }
        $this->parts[3][] = $part;
        return $this;
    }

    public function OrWhere($field, $param) {
        $part = ' or ' . $field;
        if(isset($param)) {
            if(is_array($param)) {
                $part .= ' in (' . implode(',', $param) . ')';
            } else {
                $part .= '=' . $param;
            }
        }
        $this->parts[3][] = $part;
        return $this;
    }

    public function getParts() {
        return $this->parts;
    }

    public function getSql() {
        return $this->buildQueryString($this->getParts());
    }

    private function buildQueryString($parts) {
        $sql = '';
        foreach($parts as $part) {
            if(is_array($part)) {
                $sql .= $this->buildQueryString($part);
            } else {
                $sql .= $part;
            }
        }
        return $sql;
    }

    public function execute() {
        $db = PdoConnection::getInstance();
        $stmt = $db->prepare($this->getSql());
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
