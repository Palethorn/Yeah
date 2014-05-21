<?php
namespace Yeah\Fw\Session;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NullSessionHandler
 *
 * @author david
 */
class NullSessionHandler implements \SessionHandlerInterface {
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

//put your code here
}
