<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;

class Date extends AbstractProvider{

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\DateTimeAttribute',
            'widget' => 'choice',
            'format' => IntlDateFormatter::MEDIUM,
        ));
    }

}