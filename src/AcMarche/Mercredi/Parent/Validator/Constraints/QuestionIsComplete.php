<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/03/18
 * Time: 12:59
 */

namespace AcMarche\Mercredi\Parent\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class QuestionIsComplete extends Constraint
{
    public $message_question = '{{ string }} (Indiquez la réponse dans le champ remarque à côté de la question)';

    /**
     *
     * @return array|string
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }


}
