<?php
namespace Yeah\Fw\Db;

/**
 * Class for creating query objects used in a simple CRUD
 */
class PdoQuery {

    private $parts = array();

    /**
     * Adds SELECT portion of the query
     * 
     * @param string $fields Fields to be selected
     * @return \Yeah\Fw\Db\PdoQuery
     */
    public function Select($fields) {
        $this->parts[0] = 'select ' . $fields;
        return $this;
    }
    
    /**
     * Adds FROM portion of the query
     * 
     * @param string $tables List of tables for FROM statement
     */
    public function From($tables) {
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

    /**
     * Appends conditional statements to already existing WHERE portion of the 
     * query with OR operator
     * 
     * @param string $field Field to consider
     * @param string $param Value to consider
     * @return \Yeah\Fw\Db\PdoQuery
     */
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
        $db = PdoConnection::getInstance();
        $stmt = $db->prepare($this->getSql());
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
