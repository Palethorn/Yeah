<?php
namespace Yeah\Fw\Form;

/**
 * 
 */
class ComboBoxHtmlComponent implements HtmlComponentInterface {

    private $options = array();

    private $value = null;
    
    public function __construct($options) {
        foreach($options as $key => $option) {
            $this->setOption($key, $option);
        }
    }

    public function getAllOptions() {
        return $this->options;
    }

    public function getOption($key) {
        if(isset($this->options[$key])) {
            return $this->options[$key];
        }
        return false;
    }

    public function render() {
        $html = '<select id="' . $this->getOption('id') . '"';
        if($this->getOption('classes')) {
            $html .= ' classes="';
            foreach($this->getOption('classes') as $class) {
                $html .= $class;
            }
            $html .= '"';
        }
        $html .= '>';
        foreach($this->getOption('values') as $value) {
            $html .= '<option>' . $value . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public function setOption($key, $option) {
        $this->options[$key] = $option;
    }

    public function validate() {
        
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

}
