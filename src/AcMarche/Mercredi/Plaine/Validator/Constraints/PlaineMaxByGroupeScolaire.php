<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/03/18
 * Time: 12:59.
 */

namespace AcMarche\Mercredi\Plaine\Validator\Constraints;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 */
class PlaineMaxByGroupeScolaire extends Constraint
{
    public $message = 'La journée du {{ string }} est déjà complète.';

    /**
     * @var Enfant
     *             Necessaire pour le groupe scolaire
     */
    public $enfant;

    public function __construct($options = null)
    {
        if (null !== $options && !\is_array($options)) {
            $options = [
                'enfant' => $options,
            ];
        }

        parent::__construct($options);

        if (null === $this->enfant) {
            throw new MissingOptionsException(sprintf('Either option "plaine" must be given for constraint %s', __CLASS__), ['enfant']);
        }
    }
}
