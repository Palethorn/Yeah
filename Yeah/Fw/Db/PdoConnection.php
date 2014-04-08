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
    
    public function __construct($options) {
        parent::__construct(self::$dsn, self::$username, self::$password, array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_STRINGIFY_FETCHES => false
        ));
    }

    public static function configure($dsn, $username, $password) {
        self::$dsn = $dsn;
        self::$username = $username;
        self::$password = $password;
    }
}
