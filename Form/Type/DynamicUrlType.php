<?php

namespace Core\AttributeBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicUrlType extends DynamicAbstractType{

    public function configureOptions(OptionsResolver $resolver){
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'default_protocol' => 'http',
        ));
    }

    public function getParent(){
        return 'url';
    }

    public function getName()
    {
        return 'dynamic_url';
    }

}
