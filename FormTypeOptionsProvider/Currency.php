<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Currency extends Choice{

    public function getOptions(){
        $defaultOptions = parent::getOptions();
        unset($defaultOptions['choices']);
        return $defaultOptions;
    }

}