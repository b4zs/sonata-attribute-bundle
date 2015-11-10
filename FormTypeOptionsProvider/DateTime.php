<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Core\AttributeBundle\Validator\Constraints\ConstraintWrapper;
use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;

class DateTime extends AbstractProvider{

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\DateTimeAttribute',
            'date_format' => IntlDateFormatter::MEDIUM,
            'date_widget' => 'choice',
            'time_widget' => 'choice',
            'with_minutes' => true,
            'with_seconds' => false,
        ));
    }

    protected function buildConstraintsArray($options)
    {
        $constraints = parent::buildConstraintsArray($options);
        $constraints[] = new ConstraintWrapper(new \Symfony\Component\Validator\Constraints\DateTime());
    }

}