<?php

namespace Core\AttributeBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicEmailType extends DynamicAbstractType{

    public function configureOptions(OptionsResolver $resolver){
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'trim' => true,
        ));
    }

    public function getParent(){
        return 'email';
    }

    public function getName()
    {
        return 'dynamic_email';
    }

}
