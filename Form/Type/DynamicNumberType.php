<?php

namespace Core\AttributeBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicNumberType extends DynamicAbstractType{

    public function configureOptions(OptionsResolver $resolver){
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'scale' => 3,
            'precision' => 3,
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_DOWN,
        ));
    }

    public function getParent(){
        return 'number';
    }

    public function getName()
    {
        return 'dynamic_number';
    }

}
