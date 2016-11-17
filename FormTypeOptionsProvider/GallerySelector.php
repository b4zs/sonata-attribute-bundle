<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

/**
 * NOTICE:
 * works only through the administration interface, as it internally uses sonata_type_model_list!
 */

class GallerySelector extends AbstractProvider
{
    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\GalleryAttribute',
            'data_class' => 'Core\AttributeBundle\Entity\GalleryAttribute',
        ));
    }

    public function appendConstraints($options)
    {
        $options['omit_attribute_transformer'] = true;
        $options['class'] = 'Core\AttributeBundle\Entity\GalleryAttribute';
        $options['data_class'] = 'Core\AttributeBundle\Entity\GalleryAttribute';

        return $options;
    }


}