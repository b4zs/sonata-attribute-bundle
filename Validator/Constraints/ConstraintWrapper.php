<?php

namespace Core\AttributeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConstraintWrapper extends Constraint
{

    /** @var Constraint */
    public $constraint;

    public function getDefaultOption()
    {
        return 'constraint';
    }

}