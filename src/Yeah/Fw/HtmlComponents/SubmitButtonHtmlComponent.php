<?php

namespace Yeah\Fw\HtmlComponents;

class SubmitButtonHtmlComponent extends HtmlComponentAbstract {
    
    public function render() {
        $html = '<input type="submit"';
        $html .= ' name="'. $this->getOption('name') . '"';
        $html .= ' id="'. $this->getOption('id') . '"';
        $html .= ' value="'. $this->getOption('value') . '"';
        $html .= ' classes="' . $this->getOption('classes') . '"/>';
        return $html;
    }
}
