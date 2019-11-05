<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 22/08/18
 * Time: 16:49.
 */

namespace AcMarche\Mercredi\Admin\Manager;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Tuteur;

class QuickManager
{
    /**
     * @var Enfant|null
     */
    private $enfant;

    /**
     * @var Tuteur|null
     */
    private $tuteur;

    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }

    public function setEnfant(?Enfant $enfant): void
    {
        $this->enfant = $enfant;
    }

    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function setTuteur(?Tuteur $tuteur): void
    {
        $this->tuteur = $tuteur;
    }
}
