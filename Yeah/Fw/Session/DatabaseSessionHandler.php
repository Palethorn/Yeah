<?php
namespace Yeah\Fw\Session;

class DatabaseSessionHandler implements \SessionHandlerInterface {

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

    public function open($path, $name) {
        
    }

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

    protected function create_new_session() {
        $this->last_access = strtotime(date('Y-m-d H:i:s'));
        $stmt = $this->db->prepare('insert into sessions(session_id, data, last_access) values(?, ?, ?)');
        return $stmt->execute(array($this->id, serialize($this->params), $this->last_access));
    }

    protected function get_session_from_db() {
        $stmt = $this->db->prepare('select * from sessions where session_id=?');
        $stmt->execute(array($this->id));
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $res;
    }

    public function write($id, $value) {
        if (!$this->update_session()) {
            throw new \Exception('Session does not exist!', 500, null);
        }
    }

    protected function update_session() {
        $stmt = $this->db->prepare('update sessions set data=?, last_access=? where session_id=?');
        $this->last_access = strtotime(date('Y-m-d H:i:s'));
        return $stmt->execute(array(serialize($this->params), $this->last_access, $this->id));
    }

    public function close() {
        
    }

    public function destroy($id) {
        $stmt = $this->db->prepare('delete from sessions where session_id=?');
        return $stmt->execute(array($this->id));
    }

    public function gc($maxlifetime) {
        $this->last_access = strtotime(date('Y-m-d H:i:s'));
        $time = $this->last_access - $maxlifetime;
        $stmt = $this->db->prepare('delete from sessions where last_access<?');
        return $stmt->execute(array($time));
    }

    public function setSessionParam($key, $value) {
        $this->params[$key] = $value;
    }

    public function getSessionParam($key) {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        } else {
            return false;
        }
    }

    public function removeSessionParam($key) {
        unset($this->params[$key]);
    }

}
