<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Checkbox extends AbstractProvider{

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\BooleanAttribute',
        ));
    }

}