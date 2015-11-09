<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Country extends Choice{

    public function getOptions(){
        $defaultOptions = parent::getOptions();
        unset($defaultOptions['choices']);
        return $defaultOptions;
    }

}