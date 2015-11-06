<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;

class Integer extends AbstractProvider{

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\IntegerAttribute',
            'scale' => 3,
            'precision' => 3,
            'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_DOWN,
        ));
    }

}