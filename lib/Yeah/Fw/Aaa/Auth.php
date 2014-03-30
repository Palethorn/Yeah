<?php
namespace Yeah\Fw\Aaa;

/**
 * @property Context $context Current context
 * @property SessionHandlerInterface $sessionHandler Session handler object
 */
class Auth {
    public function __construct($session_handler) {
        $this->session_handler = $session_handler;
    }
    
    public function getUser() {
        return $this->session_handler->getSessionParam('user');
    }
    
    public function setUser($user) {
        $this->session_handler->setSessionParam('user', $user);
    }
    
    public function isAuthenticated() {
        return $this->session_handler->getSessionParam('is_authenticated');
    }
    
    public function setAuthenticated($value) {
        $this->session_handler->setSessionParam('is_authenticated', $value);
    }
    
}