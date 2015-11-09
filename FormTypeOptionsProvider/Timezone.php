<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Timezone extends Choice{

    public function getOptions(){
        $defaultOptions = parent::getOptions();
        unset($defaultOptions['choices']);
        return $defaultOptions;
    }

}