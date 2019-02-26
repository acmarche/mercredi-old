<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/03/18
 * Time: 13:02
 */

namespace AcMarche\Mercredi\Parent\Validator\Constraints;

use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Parent\Manager\SanteManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class QuestionIsCompleteValidator extends ConstraintValidator
{
    /**
     * @var SanteManager
     */
    private $santeManager;

    public function __construct(SanteManager $santeManager)
    {
        $this->santeManager = $santeManager;
    }

    /**
     * Si une question demande un complement
     * Si la reponse est oui
     * Si champ remarque remplis
     *
     * @param SanteQuestion[] $questions
     * @param Constraint $constraint
     */
    public function validate($questions, Constraint $constraint)
    {
        foreach ($questions as $question) {
            if (!$this->santeManager->checkQuestionOk($question)) {
                $order = $question->getDisplayOrder() ? $question->getDisplayOrder() : 0;
                $this->context->buildViolation($constraint->message_question)
                    ->atPath('sante_fiche[questions]['.$order.'][remarque]')
                    ->setParameter('{{ string }}', $question->getIntitule().' : '.$question->getComplementLabel())
                    ->addViolation();
            }
        }
    }
}
