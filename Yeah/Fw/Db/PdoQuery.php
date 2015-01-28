<?php

namespace Yeah\Fw\Db;

/**
 * Class for creating query objects used in a simple CRUD
 * 
 * @author David Cavar
 */
class PdoQuery {

    private $parts = array();

    /**
     * @return string
     */
    public function combineArray($array) {
        return implode(',', $array);
    }

    /**
     * Adds SELECT portion of the query
     * 
     * @param string $fields Fields to be selected
     * @return \Yeah\Fw\Db\PdoQuery
     */
    public function Select($fields) {
        if(is_array($fields)) {
            $fields = $this->combineArray($fields);
        }
        $this->parts[0] = 'select ' . $fields;
        return $this;
    }

    /**
     * Adds FROM portion of the query
     * 
     * @param string $tables List of tables for FROM statement
     * @return \Yeah\Fw\Db\PdoQuery
     */
    public function From($tables) {
        if(is_array($tables)) {
            $tables = $this->combineArray($tables);
        }
        $this->parts[1] = ' from ' . $tables;
        return $this;
    }

    /**
     * Adds INNER JOIN portion of the query
     * 
     * @param string $table Table to join with
     * @param string $condition Join condition
     * @return \Yeah\Fw\Db\PdoQuery
     */
    public function InnerJoin($table, $condition) {
        $this->parts[2][] = ' inner join ' . $table . ' on ' . $condition;
        return $this;
    }

    /**
     * Adds conditional part of the query
     * 
     * @param string $field Field to consider
     * @param string $param Value to consider
     * @return \Yeah\Fw\Db\PdoQuery
     */
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

    /**
     * Appends conditional statements to already existing WHERE portion of the 
     * query with AND operator
     * 
     * @param string $field Field to consider
     * @param string $param Value to consider
     * @return \Yeah\Fw\Db\PdoQuery
     */
    public function AndWhere($field, $param = null) {
        if(!isset($this->parts[3])) {
            return $this->Where($field);
        }
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

    /**
     * Appends conditional statements to already existing WHERE portion of the 
     * query with OR operator
     * 
     * @param string $field Field to consider
     * @param string $param Value to consider
     * @return \Yeah\Fw\Db\PdoQuery
     */
    public function OrWhere($field, $param) {
        if(!isset($this->parts[3])) {
            return $this->Where($field, $param);
        }
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

    /**
     * Requrns all query parts
     * 
     * @return mixed
     */
    public function getParts() {
        return $this->parts;
    }

    /**
     * Fetches assembles query
     * 
     * @return string
     */
    public function getSql() {
        return $this->buildQueryString($this->getParts());
    }

    /**
     * Runs assembly on query parts
     * 
     * @param mixed $parts
     * @return string
     */
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

    /**
     * Executes final query
     * 
     * @return mixed
     */
    public function execute() {
        $db = new PdoConnection(array());
        $stmt = $db->prepare($this->getSql());
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
