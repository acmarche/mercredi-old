<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 9/10/18
 * Time: 10:41.
 */

namespace AcMarche\Mercredi\Plaine\Manager;

use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Entity\PlaineMax;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PlaineManager
{
    /**
     * @var ScolaireService
     */
    private $scolaireService;
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        ScolaireService $scolaireService,
        PlaineRepository $plaineRepository,
        ParameterBagInterface $parameterBag
    ) {
        $this->scolaireService = $scolaireService;
        $this->plaineRepository = $plaineRepository;
        $this->parameterBag = $parameterBag;
    }

    public function newInstance(): Plaine
    {
        $plaine = new Plaine();
        $this->initPrix($plaine);
        $this->initJours($plaine);
        $this->initMax($plaine);

        return $plaine;
    }

    public function initPrix(Plaine $plaine)
    {
        $prix_jour1 = $this->parameterBag->get('plaine_bundle_prix_jour1');
        $prix_jour2 = $this->parameterBag->get('plaine_bundle_prix_jour2');
        $prix_jour3 = $this->parameterBag->get('plaine_bundle_prix_jour3');

        $plaine->setPrix1($prix_jour1);
        $plaine->setPrix2($prix_jour2);
        $plaine->setPrix3($prix_jour3);
    }

    public function initJours(Plaine $plaine)
    {
        for ($i = 0; $i < 6; ++$i) {
            $jour = new PlaineJour();
            $jour->setPlaine($plaine);
            $plaine->addJour($jour);
        }
    }

    public function initMax(Plaine $plaine)
    {
        $groupes = $this->scolaireService::getGroupesScolaires();
        unset($groupes['premats']);

        foreach ($groupes as $groupe) {
            $max = new PlaineMax();
            $max->setGroupe($groupe);
            $max->setMaximum(40);
            $plaine->addMax($max);
        }
    }

    public function setJours(Plaine $plaine, ArrayCollection $jours)
    {
        foreach ($jours as $jour) {
            if (null != $jour->getDateJour()) {
                $jour->setPlaine($plaine);
            } else {
                $plaine->removeJour($jour); //si un champ date pas rempli
            }
        }
    }
}
