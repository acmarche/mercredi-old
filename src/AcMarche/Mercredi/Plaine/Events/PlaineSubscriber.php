<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/18
 * Time: 11:19
 */

namespace AcMarche\Mercredi\Plaine\Events;

use AcMarche\Mercredi\Plaine\Service\Mailer;
use AcMarche\Mercredi\Plaine\Service\PlaineService;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PlaineSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var PlaineService
     */
    private $plaineService;
    /**
     * @var ScolaireService
     */
    private $scolaireService;

    public function __construct(Mailer $mailer, PlaineService $plaineService, ScolaireService $scolaireService)
    {
        $this->mailer = $mailer;
        $this->plaineService = $plaineService;
        $this->scolaireService = $scolaireService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            PlaineEvent::PLAINE_SHOW => 'onPlaineShow',
            PlaineEvent::PLAINE_EDIT => 'onPlaineEdit',
            PlaineEvent::PLAINE_PAIEMENT => 'onPlainePaiement',
        ];
    }

    /**
     * @param PlaineEvent $event
     */
    public function onPlaineShow(PlaineEvent $event)
    {
    }

    /**
     * @param PlaineEvent $event
     */
    public function onPlaineEdit(PlaineEvent $event)
    {
    }

    /**
     * @param PlaineEvent $event
     * @throws \Exception
     */
    public function onPlainePaiement(PlaineEvent $event)
    {
        $plaine = $event->getPlaine();

        if ($plaine->isInscriptionOuverture()) {

            $plainePresence = $event->getPlainePresence();

            $plaine_enfant = $plainePresence->getPlaineEnfant();
            $enfant = $plaine_enfant->getEnfant();

            $email = $this->plaineService->getEmailOnPresence($plainePresence);
            $groupe = $this->scolaireService->getGroupeScolaire($enfant);

            if ($email) {
                   $this->mailer->sendConfirmationInscription($plaine, $enfant, $email, $groupe);
            } else {
                    $this->mailer->sendPasEmailPaine($plaine, $enfant);
            }
        }
    }
}
