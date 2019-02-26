<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/03/18
 * Time: 13:02
 */

namespace AcMarche\Mercredi\Plaine\Validator\Constraints;

use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Plaine\Service\PlaineService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PlaineMaxByGroupeScolaireValidator extends ConstraintValidator
{
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var PlaineService
     */
    private $plaineService;

    public function __construct(PlaineRepository $plaineRepository, PlaineService $plaineService)
    {
        $this->plaineRepository = $plaineRepository;
        $this->plaineService = $plaineService;
    }

    public function validate($values, Constraint $constraint)
    {
        $enfant = $constraint->enfant;

        foreach ($values as $value) {
            if (!$this->plaineService->isMaxByGroupScolaire($enfant, $value)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
            }
        }
    }
}
