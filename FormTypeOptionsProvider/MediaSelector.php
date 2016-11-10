<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

/**
 * NOTICE:
 * works only through the administration interface, as it internally uses sonata_type_model_list!
 */

class MediaSelector extends AbstractProvider
{
    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\MediaAttribute',
            'data_class' => 'Core\AttributeBundle\Entity\MediaAttribute',
            'context' => 'default',
            'provider' => 'sonata.media.provider.file',
        ));
    }

    public function appendConstraints($options)
    {
        $options['omit_attribute_transformer'] = true;
        $options['class'] = 'Core\AttributeBundle\Entity\MediaAttribute';
        $options['data_class'] = 'Core\AttributeBundle\Entity\MediaAttribute';

        return $options;
    }


}