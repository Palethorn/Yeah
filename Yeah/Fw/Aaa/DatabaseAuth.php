<?php

namespace Yeah\Fw\Aaa;

/**
 * @property Context $context Current context
 * @property SessionHandlerInterface $session_handler Session handler object
 */
class DatabaseAuth implements AuthInterface {

    public function __construct($options) {
        $this->session_handler = $options['session_handler'];
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
    
    public function destroy() {
        $this->session_handler->destroy(null);
    }

}
