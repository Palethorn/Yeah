<?php
namespace Yeah\Fw\Session;

/**
 * Abstract definition of PHP SessionHandlerInterface
 *
 * @author david
 */
abstract class SessionHandlerAbstract implements \SessionHandlerInterface {

    /**
     * Handles session close event
     */
    public abstract function close();

    /**
     * Handles session destroy event
     */
    public abstract function destroy($session_id);

    /**
     * Handles session garbage collect event
     */
    public abstract function gc($maxlifetime);

    /**
     * Handles session open event
     */
    public abstract function open($save_path, $name);

    /**
     * Handles session read event
     */
    public abstract function read($session_id);

    /**
     * Handles session write event
     */
    public abstract function write($session_id, $session_data);

    /**
     * Sets session variable
     */
    public abstract function setSessionParam($key, $value);

    /**
     * Retrieves session variable
     */
    public abstract function getSessionParam($key);

    /**
     * Removes session variable
     */
    public abstract function removeSessionParam($key);

}
