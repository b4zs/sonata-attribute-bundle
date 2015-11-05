<?php

namespace Core\AttributeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class DynamicAbstractType extends AbstractType implements DynamicFormTypeInterface{

    protected $attributeClass;

    /**
     * @param string $attributeClass
     */
    public function __construct($attributeClass)
    {
        $this->attributeClass = $attributeClass;
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'value_class' => null,
            'attribute_class' => $this->attributeClass,
            'disabled' => false,
            'empty_data' => '',
            'label' => '',
            'required' => true,
            'attr'  => array(
                'maxlength' => null,
                'readonly' => false,
                'placeholder' => null,
                'class' => null,
                'style' => null,
            ),
        ));
    }

    public function getOptions(){
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        return $resolver->resolve();
    }

}