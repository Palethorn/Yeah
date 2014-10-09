<?php

namespace Yeah\Fw\Form;

/**
 * Facade for form rendering
 */
class FormFacade {

    static public $formConfigContainer = null;

    public static function create($name) {
        $form = new Form();
        $form->configure(self::getFormConfigContainer()->getOption($name));
        return $form->render();
    }

    /**
     * 
     * @return \Yeah\Fw\ParameterHolder\SimpleParameterHolder
     */
    public static function getFormConfigContainer() {
        if(self::$formConfigContainer === null) {
            self::$formConfigContainer = new \Yeah\Fw\ParameterHolder\SimpleParameterHolder();
        }
        return self::$formConfigContainer;
    }

    public static function addConfig($name, $config) {
        self::getFormConfigContainer()->setOption($name, $config);
    }

}
