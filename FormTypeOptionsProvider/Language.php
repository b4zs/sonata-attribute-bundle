<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Language extends Choice{

    public function getOptions(){
        $defaultOptions = parent::getOptions();
        unset($defaultOptions['choices']);
        return $defaultOptions;
    }

}