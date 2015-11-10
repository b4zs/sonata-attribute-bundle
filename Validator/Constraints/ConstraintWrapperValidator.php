<?php

namespace Core\AttributeBundle\Validator\Constraints;

use Core\AttributeBundle\Entity\Attribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConstraintWrapperValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint){

        if($value instanceof Attribute){
            $value = $value->getValue();
        }

        /** @var Constraint $originalConstraint */
        $originalConstraint = $constraint->constraint;
        $validator = $originalConstraint->validatedBy();

        /** @var \Symfony\Component\Validator\ConstraintValidatorInterface $validator */
        $validator = new $validator();
        $validator->initialize($this->context);
        $validator->validate($value, $originalConstraint);

    }

}