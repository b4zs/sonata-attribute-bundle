<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Core\AttributeBundle\Validator\Constraints\ConstraintWrapper;

class Url extends AbstractProvider{

    public function getOptions(){
        $defaultOptions = parent::getOptions();

        return array_merge($defaultOptions, array(
            'attribute_class' => 'Core\AttributeBundle\Entity\StringAttribute',
            'default_protocol' => 'http',
        ));
    }

    protected function buildConstraintsArray($options)
    {
        $constraints = parent::buildConstraintsArray($options);
        $constraints[] = new ConstraintWrapper(new \Symfony\Component\Validator\Constraints\Url());
    }


}