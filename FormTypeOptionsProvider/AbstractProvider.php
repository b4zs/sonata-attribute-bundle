<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

abstract class AbstractProvider implements ProviderInterface{

    public function getOptions(){
        return array(
            'data_class' => null,
            'attribute_class' => null,
            'disabled' => false,
            'empty_data' => '',
            'label' => null,
            'required' => true,
            'attr'  => array(
                'maxlength' => null,
                'readonly' => false,
                'placeholder' => null,
                'class' => null,
                'style' => null,
            ),
        );
    }

    public function getOption($option){
        $options = $this->getOptions();
        return array_key_exists($option, $options)?$options[$option]:null;
    }

    public function hasOption($option){
        $options = $this->getOptions();
        return array_key_exists($option, $options);
    }

}