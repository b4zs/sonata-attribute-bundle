<?php


namespace Core\AttributeBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TypeClass extends Constraint
{

    public $message = 'A type with the given name (%name%) already exists below its parent.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'core_attribute.validator.constraints.type_class';
    }

}