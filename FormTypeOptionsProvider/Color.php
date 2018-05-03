<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Color extends AbstractProvider{

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_replace_recursive($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\StringAttribute',
            'trim' => true,
            'attr' => array(
                'maxlength' => null,
                'placeholder' => null,
            )
        ));
    }

}