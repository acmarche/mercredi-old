<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/03/18
 * Time: 13:02
 */

namespace AcMarche\Mercredi\Parent\Validator\Constraints;

use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Service\TuteurUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TelephoneIsCompleteValidator extends ConstraintValidator
{
    /**
     * @param Tuteur $tuteur
     * @param Constraint $constraint
     */
    public function validate($tuteur, Constraint $constraint)
    {
        if (TuteurUtils::hasTelephone($tuteur) === false) {
            $this->context->buildViolation($constraint->message)
                //->atPath('telephone')
                // ->atPath('gsm')
                //->setParameter('{{ string }}', $tuteur)
                ->addViolation();
        }
    }
}
