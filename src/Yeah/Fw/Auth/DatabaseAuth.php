<?php

namespace Yeah\Fw\Auth;

/**
 * Yeah authentication class
 * Gets and sets user authentication data
 * 
 * @property SessionHandlerInterface $session_handler Session handler object
 * 
 * @author David Cavar
 */
class DatabaseAuth implements AuthInterface {

    /**
     * Constructs object
     * 
     * @param mixed $options Contains options necesarry for object initialization
     */
    public function __construct(\SessionHandlerInterface $session) {
        $this->session_handler = $session;
    }

    /**
     * Returns authenticated user
     * 
     * @return User
     */
    public function getUser() {
        return $this->session_handler->getSessionParam('user');
    }

    /**
     * Sets authenticated user
     * 
     * @param User $user User object
     */
    public function setUser($user) {
        $this->session_handler->setSessionParam('user', $user);
    }

    /**
     * Returns true if user is authenticated
     * Returns false if user is not authenticated
     * 
     * @return bool
     */
    public function isAuthenticated() {
        return $this->session_handler->getSessionParam('is_authenticated');
    }

    /**
     * Sets authenticated flag
     * 
     * @param bool $value
     */
    public function setAuthenticated($value) {
        $this->session_handler->setSessionParam('is_authenticated', $value);
    }
    
    /**
     * {@inheritdoc }
     */
    public function isAuthorized(\Yeah\Fw\Routing\Route\RouteInterface $route) {
        return true;
    }

    /**
     * Destroys current session
     */
    public function destroy() {
        $this->session_handler->destroy(null);
    }

}
