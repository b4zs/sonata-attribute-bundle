<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Core\AttributeBundle\Validator\Constraints\ConstraintWrapper;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                'readonly' => false,
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

    protected function buildConstraintsArray($options){

        $constraints = array();

        if(array_key_exists('required', $options) && $options['required'] === true){
            $constraints[] = new ConstraintWrapper(new NotBlank());
        }

        return $constraints;
    }

    /**
     * @param array
     * @return array
     */
    public function appendConstraints($options)
    {
        return array_merge($options, array('constraints' => $this->buildConstraintsArray($options)));
    }


}
