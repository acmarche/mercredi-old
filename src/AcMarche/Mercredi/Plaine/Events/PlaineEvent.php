<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/18
 * Time: 11:27.
 */

namespace AcMarche\Mercredi\Plaine\Events;

use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use Symfony\Component\EventDispatcher\Event;

class PlaineEvent extends Event
{
    const PLAINE_SHOW = 'plaine_show';
    const PLAINE_EDIT = 'plaine_edit';
    const PLAINE_PAIEMENT = 'plaine_paiement';

    protected $plaine;
    protected $plainePresence;

    public function __construct(Plaine $plaine, PlainePresence $plainePresence = null)
    {
        $this->plaine = $plaine;
        $this->plainePresence = $plainePresence;
    }

    /**
     * @return Plaine
     */
    public function getPlaine()
    {
        return $this->plaine;
    }

    /**
     * @return PlainePresence
     */
    public function getPlainePresence()
    {
        return $this->plainePresence;
    }
}
