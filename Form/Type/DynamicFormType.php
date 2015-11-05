<?php

namespace Core\AttributeBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicFormType extends DynamicAbstractType{

    function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'attribute_class' => $this->attributeClass,
            'value_class' => $this->attributeClass,
            'label' => '',
            'attr'  => array(
                'maxlength' => null,
                'readonly' => false,
                'placeholder' => null,
                'class' => null,
                'style' => null,
            )
        ));
    }

    public function getParent(){
        return 'form';
    }

    public function getName()
    {
        return 'dynamic_form';
    }

}
