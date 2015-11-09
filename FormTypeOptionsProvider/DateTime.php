<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;

class DateTime extends Time{

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        unset($defaultOptions['widget']);

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\DateTimeAttribute',
            'date_format' => IntlDateFormatter::MEDIUM,
            'date_widget' => 'choice',
            'time_widget' => 'choice',
        ));
    }

}