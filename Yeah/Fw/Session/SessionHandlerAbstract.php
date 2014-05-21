<?php
namespace Yeah\Fw\Session;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SessionHandlerAbstract
 *
 * @author david
 */
abstract class SessionHandlerAbstract {

    public abstract function close();

    public abstract function destroy($session_id);

    public abstract function gc($maxlifetime);

    public abstract function open($save_path, $name);

    public abstract function read($session_id);

    public abstract function write($session_id, $session_data);

    public abstract function setSessionParam($key, $value);

    public abstract function getSessionParam($key);

    public abstract function removeSessionParam($key);

}
