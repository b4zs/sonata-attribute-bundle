<?php

namespace Core\AttributeBundle\FormTypeOptionsProvider;

use Core\AttributeBundle\Validator\Constraints\ConstraintWrapper;

class Email extends Text{

    public function getOptions(){
        return parent::getOptions();
    }

    protected function buildConstraintsArray($options)
    {
        $constraints = parent::buildConstraintsArray($options);
        $constraints[] = new ConstraintWrapper(new \Symfony\Component\Validator\Constraints\Email());

        return $constraints;
    }

}
