<?php
namespace Yeah\Fw\Session;

/**
 * Session handler class
 * 
 * @property array $params Session variables
 * @property string $id Session identifier
 * @property string $name Session name as it appears in cookie
 * @property int $last_access Session timestamp
 */
class DatabaseSessionHandler extends SessionHandlerAbstract {

    private $params = array();
    private $id = '';
    private $name = 'SpoilersSession';
    private $last_access = null;
    private $db = null;

    public function __construct($options) {
        if($options['static_url'] == 'http://' . $_SERVER['HTTP_HOST']) {
            return;
        }
        $db_conf = $options['database'];
        register_shutdown_function('session_write_close');
        ini_set("session.gc_probability", 100);
        ini_set("session.gc_divisor", 1);
        $id = isset($_COOKIE['SpoilersSession']) ? $_COOKIE['SpoilersSession'] : (isset($_GET['client_id']) ? $_GET['client_id'] : \Yeah\Fw\Toolbox\Various::generateRandomString(32));
        $this->id = $id;
        session_name($this->name);
        session_id($this->id);

        session_set_save_handler(
                array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc')
        );
        
        $this->db = new \PDO($db_conf['dsn'], $db_conf['db_user'], $db_conf['db_password']);
        session_start();
    }

    /**
     * Handles session open
     * 
     * @param string $path Path to session container
     * @param string $name Session name
     */
    public function open($path, $name) {
        
    }

    /**
     * Reads session data from container
     * 
     * @param string $id
     * @throws \Exception
     */
    public function read($id) {
        $session = $this->get_session_from_db();
        if (!$session) {
            $session = $this->create_new_session();
            if (!$session) {
                throw new \Exception('Could not create session.', 500, null);
            }
        } else {
            $this->params = unserialize($session['data']);
            $this->last_access = $session['last_access'];
        }
    }

    /**
     * Creates new session
     * 
     * @return bool
     */
    protected function create_new_session() {
        $this->last_access = strtotime(date('Y-m-d H:i:s'));
        $stmt = $this->db->prepare('insert into sessions(session_id, data, last_access) values(?, ?, ?)');
        return $stmt->execute(array($this->id, serialize($this->params), $this->last_access));
    }

    /**
     * Retrieves session from database
     * 
     * @return array
     */
    protected function get_session_from_db() {
        $stmt = $this->db->prepare('select * from sessions where session_id=?');
        $stmt->execute(array($this->id));
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $res;
    }

    /**
     * Writes session to container
     * 
     * @param string $id
     * @param mixed $value
     * @throws \Exception
     */
    public function write($id, $value) {
        if (!$this->update_session()) {
            throw new \Exception('Session does not exist!', 500, null);
        }
    }

    /**
     * Updates existing session in container
     * 
     * @return bool
     */
    protected function update_session() {
        $stmt = $this->db->prepare('update sessions set data=?, last_access=? where session_id=?');
        $this->last_access = strtotime(date('Y-m-d H:i:s'));
        return $stmt->execute(array(serialize($this->params), $this->last_access, $this->id));
    }

    /**
     * Handles session close
     */
    public function close() {
        
    }

    /**
     * Removes session from container
     * 
     * @param string $id
     * @return bool
     */
    public function destroy($id) {
        $stmt = $this->db->prepare('delete from sessions where session_id=?');
        return $stmt->execute(array($this->id));
    }

    /**
     * Cleans old sessions
     * 
     * @param int $maxlifetime Timestamp which specifies maximum session duration
     *
     *  @return bool
     */
    public function gc($maxlifetime) {
        $this->last_access = strtotime(date('Y-m-d H:i:s'));
        $time = $this->last_access - $maxlifetime;
        $stmt = $this->db->prepare('delete from sessions where last_access<?');
        return $stmt->execute(array($time));
    }

    /**
     * Sets session variable in container
     * 
     * @param string $key
     * @param mixed $value
     */
    public function setSessionParam($key, $value) {
        $this->params[$key] = $value;
    }

    /**
     * Retrieves session variable from container
     * 
     * @param string $key
     * @return boolean|mixed
     */
    public function getSessionParam($key) {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        } else {
            return false;
        }
    }

    /**
     * Removes session variable from container
     * 
     * @param string $key
     */
    public function removeSessionParam($key) {
        unset($this->params[$key]);
    }

}
