<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Symfony\Component\Form\Extension\Core\Type\TimezoneType;

class Timezone extends Choice{

    public function getOptions(){
        $defaultOptions = parent::getOptions();
        unset($defaultOptions['choices']);
        return $defaultOptions;
    }

    public function getPreferredOptions()
    {
        return TimezoneType::getTimezones();
    }

}
