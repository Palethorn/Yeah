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

    /**
     * {@inheritdoc}
     */
    public function close() {
        
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($session_id) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function open($save_path, $name) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function read($session_id) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function write($session_id, $session_data) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function setSessionParam($key, $value) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionParam($key) {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function removeSessionParam($key) {
        
    }

}
