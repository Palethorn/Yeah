<?php

namespace Yeah\Fw\HtmlComponents;

class TextAreaHtmlComponent extends HtmlComponentAbstract {
    
    public function render() {
        $html = '<div><label for="' . $this->getOption('id') . '">' . $this->getOption('label') . '</label></div>';
        $html .= '<div class="' . $this->getOption('classes') . '">';
        $html .= '<textarea';
        $html .= ' name="'. $this->getOption('name') . '"';
        $html .= ' id="'. $this->getOption('id') . '"';
        $html .= ' value="'. $this->getOption('value') . '">';
        $html .= '</textarea>';
        $html .= '</div>';
        return $html;
    }
}
