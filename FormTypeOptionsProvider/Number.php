<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;

class Number extends Integer{

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\IntegerAttribute', //@todo implement FloatAttribute and change this to that
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_DOWN,
        ));
    }

}