<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Time extends AbstractProvider{

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\DateTimeAttribute',
            'widget' => 'choice',
            'with_minutes' => true,
            'with_seconds' => false,
        ));
    }

}