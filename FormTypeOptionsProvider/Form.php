<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

class Form extends AbstractProvider{

    public function getOptions(){
        return array(
            'success_flash' => 'message.form_submitted',
            'attribute_class' => 'Core\AttributeBundle\Entity\CollectionAttribute',
            'data_class' => 'Core\AttributeBundle\Entity\CollectionAttribute',
            'label' => null,
            'attr'  => array(
                'class' => null,
                'style' => null,
            )
        );
    }

}