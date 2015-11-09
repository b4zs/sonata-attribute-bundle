<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Choice extends AbstractProvider{

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\JsonAttribute',
            'choices' => array(),
            'preferred_choices' => array(),
            'placeholder' => null,
            'empty_value' => null,
            'expanded' => false,
            'multiple' => false,
        ));
    }

}