<?php

namespace Core\AttributeBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicIntegerType extends DynamicAbstractType{

    public function configureOptions(OptionsResolver $resolver){
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'scale' => 3,
            'precision' => 3,
            'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_DOWN,
        ));
    }

    public function getParent(){
        return 'integer';
    }

    public function getName()
    {
        return 'dynamic_integer';
    }

}
