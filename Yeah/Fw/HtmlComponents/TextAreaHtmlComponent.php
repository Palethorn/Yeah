<?php

namespace Yeah\Fw\HtmlComponents;

class TextAreaHtmlComponent extends HtmlComponentAbstract {
    
    public function render() {
        $html = '<textarea';
        $html .= ' name="'. $this->getOption('name') . '"';
        $html .= ' id="'. $this->getOption('id') . '"';
        $html .= ' value="'. $this->getOption('value') . '"';
        $html .= ' classes="' . $this->getOption('classes') . '"></textarea>';
        return $html;
    }
}
