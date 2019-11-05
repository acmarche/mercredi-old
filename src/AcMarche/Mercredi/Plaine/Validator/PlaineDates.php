<?php

namespace AcMarche\Mercredi\Plaine\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PlaineDates extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Veuillez au moins encoder une date';
}
