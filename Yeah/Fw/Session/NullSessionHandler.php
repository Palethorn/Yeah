<?php

namespace Yeah\Fw\Session;

/**
 * This class does not provide any implementations. Should be used for applications
 * with no session support.
 * 
 * @see DatabaseSessionHandler
 * @see SessionHandlerAbstract
 * 
 */
class NullSessionHandler extends SessionHandlerAbstract {

    public function close() {
        
    }

    public function destroy($session_id) {
        
    }

    public function gc($maxlifetime) {
        
    }

    public function open($save_path, $name) {
        
    }

    public function read($session_id) {
        
    }

    public function write($session_id, $session_data) {
        
    }

    public function setSessionParam($key, $value) {
        
    }

    public function getSessionParam($key) {
        return false;
    }

    public function removeSessionParam($key) {
        
    }
}
