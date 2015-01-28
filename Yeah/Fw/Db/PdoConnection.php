<?php

namespace Yeah\Fw\Db;

/**
 * PDO connection class
 * 
 * @author David Cavar
 */
class PdoConnection extends \PDO {

    private static $instance = null;
    private static $dsn = null;
    private static $username = null;
    private static $password = null;

    /**
     * Creates PdoConnection object
     * 
     * @param mixed $options PdoConnection options
     */
    public function __construct($options) {
        parent::__construct(self::$dsn, self::$username, self::$password, array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_STRINGIFY_FETCHES => false
        ));
    }

    /**
     * Called from \Yeah\Fw\Db\PdoAdapter
     * Sets global database options
     * 
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
    public static function configure($dsn, $username, $password) {
        self::$dsn = $dsn;
        self::$username = $username;
        self::$password = $password;
    }

}
