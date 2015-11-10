<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Core\AttributeBundle\Validator\Constraints\ConstraintWrapper;
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

    protected function buildConstraintsArray($options)
    {
        $constraints = parent::buildConstraintsArray($options);
        $constraints[] = new ConstraintWrapper(new \Symfony\Component\Validator\Constraints\Date());
    }

}