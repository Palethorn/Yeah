<?php
namespace Yeah\Fw\HtmlComponents;

/**
 * 
 */
class ComboBoxHtmlComponent extends HtmlComponentAbstract {

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

}
