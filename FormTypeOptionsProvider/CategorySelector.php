<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Doctrine\ORM\EntityRepository;

/**
 * NOTICE:
 * works only through the administration interface, as it internally uses sonata_type_model_list!
 */
class CategorySelector extends AbstractProvider
{
    public function getOptions()
    {
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\CategoryAttribute',
            'data_class' => 'Core\AttributeBundle\Entity\CategoryAttribute',
        ));
    }

    public function appendConstraints($options)
    {
        $options['omit_attribute_transformer'] = true;
        $options['class'] = 'Core\AttributeBundle\Entity\CategoryAttribute';
        $options['data_class'] = 'Core\AttributeBundle\Entity\CategoryAttribute';

        return $options;
    }


}