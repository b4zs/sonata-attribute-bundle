<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Choice extends AbstractProvider implements ChoiceProviderInterface{

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

    public function getPreferredOptions()
    {
        return array();
    }

}
