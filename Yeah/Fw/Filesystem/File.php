<?php

namespace Yeah\Fw\Filesystem;

/**
 * Handles simple filesystem operations. Create, write and append.
 * 
 * @author David Cavar
 */
class File {

    private $fp;
    private $mode;
    private $location;

    public function __construct($location, $mode = 'r') {
        $this->open($location, $mode);
        if($this->opened()) {
            $this->mode = $mode;
            $this->location = $location;
        }
    }

    public function open($location, $mode) {
        $this->fp = fopen($location, $mode);
    }

    public function write($data, $length = null) {
        if($this->mode == 'r') {
            throw new \Exception('Invalid filemode.', 500, null);
        }
        if($this->opened()) {
            if($length == null) {
                $length = strlen($data);
            }
            fwrite($this->fp, $data, $length);
        }
    }

    public function read($length = 1024) {
        if(strstr($this->mode, 'r') && $this->opened()) {
            return fread($this->fp, $length);
        }
        throw new \Exception('Invalid operation.', 500, null);
    }

    public function opened() {
        if($this->fp) {
            return true;
        }
        return false;
    }

    public function getSize() {
        return filesize($this->location);
    }

    public function getLocation() {
        return $this->location;
    }

    public function __destruct() {
        fclose($this->fp);
        $this->fp = null;
    }

}
