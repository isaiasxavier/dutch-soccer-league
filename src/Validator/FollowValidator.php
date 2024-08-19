<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FollowValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value->getUser()) {
            $this->context->buildViolation('The USER cannot be null.')
                ->addViolation();
        }

        if (null === $value->getTeam()) {
            $this->context->buildViolation('The TEAM cannot be null.')
                ->addViolation();
        }
    }
}
