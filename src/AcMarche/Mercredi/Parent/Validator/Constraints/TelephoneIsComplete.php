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
class TelephoneIsComplete extends Constraint
{
    public $message = 'Au moins un numéro de téléphone doit être renseigné.';

    /**
     * Pour pouvoir l'appliquer sur l'entity
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }


}
