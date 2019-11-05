<?php

namespace AcMarche\Mercredi\Plaine\Validator;

use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PlaineDatesValidator extends ConstraintValidator
{
    /**
     * @param PlaineJour[] $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \AcMarche\Mercredi\Plaine\Validator\PlaineDates */

        $count = 0;
        foreach ($value as $plaineJour) {
            if ($plaineJour->getDateJour() instanceof \DateTime) {
                $count++;
            }
        }

        if ($count === 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
