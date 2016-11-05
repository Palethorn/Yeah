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

    /**
     * Create new instance
     * 
     * @param string $location
     * @param string $mode
     */
    public function __construct($location, $mode = 'r') {
        $this->open($location, $mode);
        if($this->opened()) {
            $this->mode = $mode;
            $this->location = $location;
        }
    }

    /**
     * Opens file handler
     * 
     * @param string $location
     * @param string $mode
     */
    public function open($location, $mode) {
        $this->fp = fopen($location, $mode);
    }

    /**
     * Writes data to file
     * 
     * @param mixed $data
     * @param int $length
     * @return int
     * @throws \Exception
     */
    public function write($data, $length = null) {
        if($this->mode == 'r') {
            throw new \Exception('Invalid filemode.', 500, null);
        }

        if($this->opened()) {

            if($length == null) {
                $length = strlen($data);
            }

            $nob = fwrite($this->fp, $data, $length);
            fflush($this->fp);
            return $nob;
        }
        return 0;
    }

    /**
     * Reads file content
     * 
     * @param int $length
     * @return mixed
     * @throws \Exception
     */
    public function read($length = 1024) {
        if(\Yeah\Fw\Toolbox\PhpApi::strpos($this->mode, array('r', 'w+', 'x+', 'a+', 'c+')) !== false && $this->opened()) {
            return fread($this->fp, $length);
        }
        throw new \Exception('Invalid operation.', 500, null);
    }

    /**
     * Check if file is already opened
     * 
     * @return boolean
     */
    public function opened() {
        if($this->fp) {
            return true;
        }
        return false;
    }

    /**
     * Returns file size
     * 
     * @return int
     */
    public function getSize() {
        return filesize($this->location);
    }

    /**
     * Return file location
     * 
     * @return string
     */
    public function getLocation() {
        return $this->location;
    }
    
    /**
     * Closes file handler
     */
    public function close() {
        if($this->fp) {
            fclose($this->fp);
        }
        $this->fp = null;
    }

    /**
     * Closes file handler on exit
     */
    public function __destruct() {
        $this->close();
    }

}
