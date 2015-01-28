<?php
namespace Yeah\Fw\Serializing;

/**
 * Serializable interface which should be implemented in all classes which are
 * deemed serializable
 *
 * @author david
 */
interface Serializable {
    
    /**
     * Serializes current object
     * 
     * @return string
     */
    function serialize();
    
    /**
     * Unserializes string into property values of the current object
     */
    function unserialize();
}
