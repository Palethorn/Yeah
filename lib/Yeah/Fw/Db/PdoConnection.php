<?php
namespace Yeah\Fw\Db;

/**
 * @property PDO $instance PDO object instance
 */

class PdoConnection extends \PDO {

    private static $instance = null;
    private static $dsn = null;
    private static $username = null;
    private static $password = null;
    
    public function __construct($dsn, $username, $password) {
        parent::__construct($dsn, $username, $password, array());
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
    }

    public static function configure($dsn, $username, $password) {
        self::$dsn = $dsn;
        self::$username = $username;
        self::$password = $password;
    }
    /**
     * @return PdoConnection
     */
    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new PdoConnection(self::$dsn, self::$username, self::$password);
        }
        return self::$instance;
    }
}
