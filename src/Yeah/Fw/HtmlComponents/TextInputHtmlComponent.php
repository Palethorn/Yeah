<?php

namespace Yeah\Fw\HtmlComponents;

class TextInputHtmlComponent extends HtmlComponentAbstract {
    
    public function render() {
        $html = '<div><label for="' . $this->getOption('id') . '">';
        $html .= $this->getOption('label') . '</label></div>';
        $html .= '<div><input type="text"';
        $html .= ' name="'. $this->getOption('name') . '"';
        $html .= ' id="'. $this->getOption('id') . '"';
        $html .= ' value="'. $this->getOption('value') . '"';
        $html .= ' classes="' . $this->getOption('classes') . '"/></div>';
        return $html;
    }
}
