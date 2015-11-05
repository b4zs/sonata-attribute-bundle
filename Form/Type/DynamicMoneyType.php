<?php

namespace Core\AttributeBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicMoneyType extends DynamicAbstractType{

    public function configureOptions(OptionsResolver $resolver){
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'currency' => 'EUR',
        ));
    }

    public function getParent(){
        return 'text';
    }

    public function getName()
    {
        return 'dynamic_money';
    }

}
