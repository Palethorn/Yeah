<?php
namespace Yeah\Fw\Serializing;

class Serializer {

    protected $format = null;

    public function __construct($format) {
        $this->format = $format;
    }

    public function serialize($array) {

        if($this->format === "json") {
            return $this->serializeJson($array);
        }
        if($this->format === "xml") {
            return $this->serializeXml($array);
        }
        if($this->format === "html") {
            return $this->serializePlain($array);
        }
    }

    public function serializeJson($array) {
        return json_encode($array);
    }

    public function serializePlain($array) {
        if(is_array($array)) {
            return implode(' ', $array);
        } else {
            return $array;
        }
    }

    public function serializeXml($array) {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><response/>');
        foreach ($array as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    array_to_xml($value, $subnode);
                } else {
                    $subnode = $xml->addChild("$key");
                    array_to_xml($value, $subnode);
                }
            } else {
                $xml->addChild("$key", "$value");
            }
        }
    }

    public function unserialize($data) {
        if($this->format === "json") {
            return $this->unserializeJson($data);
        }
        if($this->format === "xml") {
            return $this->unserializeXml($data);
        }
    }

    public function unserializeJson($data) {
        return json_decode($data, true);
    }

    public function unserializeXml($data) {
        return json_decode($data, true);
    }

}
