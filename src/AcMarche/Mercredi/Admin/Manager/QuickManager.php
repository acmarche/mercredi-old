<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 22/08/18
 * Time: 16:49
 */

namespace AcMarche\Mercredi\Admin\Manager;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Tuteur;

class QuickManager
{
    /**
     * @var Enfant|null $enfant
     */
    private $enfant;

    /**
     * @var Tuteur|null $tuteur
     */
    private $tuteur;

    /**
     * @return Enfant|null
     */
    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }

    /**
     * @param Enfant|null $enfant
     */
    public function setEnfant(?Enfant $enfant): void
    {
        $this->enfant = $enfant;
    }

    /**
     * @return Tuteur|null
     */
    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    /**
     * @param Tuteur|null $tuteur
     */
    public function setTuteur(?Tuteur $tuteur): void
    {
        $this->tuteur = $tuteur;
    }

}