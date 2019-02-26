<?php

namespace AcMarche\Mercredi\Admin\Tests\Facture;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\JourRepository;
use AcMarche\Mercredi\Admin\Repository\PresenceRepository;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use AcMarche\Mercredi\Admin\Service\OrdreService;
use AcMarche\Mercredi\Admin\Tests\BaseUnit;

/**
 *
 * Test le calcul de l'ordre suivant les presences
 */
class OrdreTest extends BaseUnit
{
    /**
     * @var JourRepository $jourRepository
     */
    private $jourRepository;
    /**
     * @var EnfantRepository $enfantRepository
     */
    private $enfantRepository;
    /**
     * @var TuteurRepository $tuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var PresenceRepository $presenceRepository
     */
    private $presenceRepository;
    /**
     * @var Tuteur $philippe
     */
    private $philippe;
    /**
     * @var Enfant $lisa
     */
    private $lisa;
    /**
     * @var Enfant $marie
     */
    private $marie;
    /**
     * @var Enfant $arwen
     */
    private $arwen;
    /**
     * @var Enfant $zora
     */
    private $zora;
    /**
     * @var OrdreService $ordreService
     */
    private $ordreService;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->em = $container
            ->get('doctrine')
            ->getManager();

        $this->ordreService = $this->createMock(OrdreService::class);
        $this->ordreService = $container->get(OrdreService::class);

        $this->presenceRepository = $this->em->getRepository(Presence::class);
        $this->jourRepository = $this->em->getRepository(Jour::class);
        $this->tuteurRepository = $this->em->getRepository(Tuteur::class);
        $this->enfantRepository = $this->em->getRepository(Enfant::class);

        $this->lisa = $this->enfantRepository->findOneBy(['slugname' => 'michel_lisa']);
        $this->assertEquals('Michel', $this->lisa->getNom(), 'Lisa pas trouve');
        $this->marie = $this->enfantRepository->findOneBy(['slugname' => 'michel_marie']);
        $this->assertEquals('Michel', $this->marie->getNom(), 'Marie pas trouve');
        $this->arwen = $this->enfantRepository->findOneBy(['slugname' => 'michel_arwen']);
        $this->assertEquals('Michel', $this->arwen->getNom(), 'Arwen pas trouve');
        $this->philippe = $this->tuteurRepository->findOneBy(['slugname' => 'michel_philippe']);
        $this->assertEquals('Michel', $this->philippe->getNom(), 'Michel pas trouve');
        $this->zora = $this->enfantRepository->findOneBy(['slugname' => 'michel_zora']);
        $this->assertEquals('Michel', $this->lisa->getNom(), 'Zora pas trouve');
    }

    /**
     * Arwen (3) Lisa(2) Marie(1)
     * Tous la
     */
    public function testOrdreTous()
    {
        $datePresence = '2016-10-05';
        $jour = $this->getJour($datePresence);

        if (!$jour) {
            $this->assertNotEmpty($jour);
        }

        $presence = $this->getPresence($jour, $this->marie, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }

        $ordre = $this->ordreService->getOrdre($presence);

        $this->assertEquals(1, $ordre);

        $presence = $this->getPresence($jour, $this->lisa, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }

        $ordre = $this->ordreService->getOrdre($presence);
        $this->assertEquals(2, $ordre);

        $presence = $this->getPresence($jour, $this->arwen, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }

        $ordre = $this->ordreService->getOrdre($presence);
        $this->assertEquals(3, $ordre);
    }

    /**
     *
     * Lisa seul doit valoir 1
     */
    public function testOrdreLisa()
    {
        $datePresence = '2016-11-11';
        $jour = $this->getJour($datePresence);

        if (!$jour) {
            $this->assertNotEmpty($jour);
        }

        $presence = $this->getPresence($jour, $this->lisa, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }

        $ordre = $this->ordreService->getOrdre($presence);

        $this->assertEquals(1, $ordre);
    }

    /**
     * Arwen(3) est seul
     * Doit valoir 1
     *
     */
    public function testOrdreArwen()
    {
        $datePresence = '2016-10-17';
        $jour = $this->getJour($datePresence);

        if (!$jour) {
            $this->assertNotEmpty($jour);
        }

        $presence = $this->getPresence($jour, $this->arwen, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }

        $this->assertEquals(
            $datePresence,
            $presence->getJour()->getDateJour()->format('Y-m-d'),
            "$datePresence pas trouve 2"
        );

        $ordre = $this->ordreService->getOrdre($presence);

        $this->assertEquals(1, $ordre);
    }

    /**
     * Lisa(2) et Arwen(3) le 2016-10-19
     * Lisa doit valoir 1
     * Arwen doit valoir 2
     */
    public function testOrdreLisaArwen()
    {
        $datePresence = '2016-10-19';
        $jour = $this->getJour($datePresence);

        if (!$jour) {
            $this->assertNotEmpty($jour);
        }

        $presence = $this->getPresence($jour, $this->lisa, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }
        $ordre = $this->ordreService->getOrdre($presence);
        $this->assertEquals(1, $ordre);

        $presence = $this->getPresence($jour, $this->arwen, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }

        $ordre = $this->ordreService->getOrdre($presence);
        $this->assertEquals(2, $ordre);
    }

    /**
     * Marie(1) et Arwen(3) le 2016-10-26
     * Marie doit valoir 1
     * Arwen doit valoir 2
     */
    public function testOrdreMarieArwen()
    {
        $datePresence = '2016-10-26';
        $jour = $this->getJour($datePresence);

        if (!$jour) {
            $this->assertNotEmpty($jour);
        }

        $presence = $this->getPresence($jour, $this->marie, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }
        $ordre = $this->ordreService->getOrdre($presence);
        $this->assertEquals(1, $ordre);

        $presence = $this->getPresence($jour, $this->arwen, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }

        $ordre = $this->ordreService->getOrdre($presence);
        $this->assertEquals(2, $ordre);
    }

    /**
     * Marie(1) et lisa(2) le 2016-10-29
     * Marie absente
     * Lisa doit valoir 1
     */
    public function testOrdreMarieAbsent()
    {
        $datePresence = '2016-10-29';
        $jour = $this->getJour($datePresence);

        if (!$jour) {
            return;
        }

        $presence = $this->getPresence($jour, $this->marie, $this->philippe);
        if (!$presence) {
            return;
        }

        $ordre = $this->ordreService->getOrdre($presence);
        $this->assertEquals(1, $ordre);

        $presence = $this->getPresence($jour, $this->lisa, $this->philippe);
        if (!$presence) {
            return;
        }

        $ordre = $this->ordreService->getOrdre($presence);
        $this->assertEquals(1, $ordre);
    }

    /**
     * Lisa (2) Zora(3)
     * Doit valoir 1 et 2
     *
     */
    public function testOrdreZoraLisa()
    {
        $datePresence = '2016-10-12';
        $jour = $this->getJour($datePresence);

        if (!$jour) {
            return;
        }

        $presence = $this->getPresence($jour, $this->lisa, $this->philippe);
        if (!$presence) {
            return;
        }

        $this->assertEquals(
            $datePresence,
            $presence->getJour()->getDateJour()->format('Y-m-d'),
            "$datePresence pas trouve 2"
        );

        $ordre = $this->ordreService->getOrdre($presence);

        $this->assertEquals(1, $ordre);

        $presence = $this->getPresence($jour, $this->zora, $this->philippe);
        if (!$presence) {
            return;
        }

        $this->assertEquals(
            $datePresence,
            $presence->getJour()->getDateJour()->format('Y-m-d'),
            "$datePresence pas trouve 2"
        );

        $ordre = $this->ordreService->getOrdre($presence);

        $this->assertEquals(2, $ordre);
    }

    /**
     * Zora(3) seul
     * Doit valoir 1
     *
     */
    public function testOrdreZora()
    {
        $datePresence = '2016-11-01';
        $jour = $this->getJour($datePresence);

        if (!$jour) {
            $this->assertNotEmpty($jour);
        }

        $presence = $this->getPresence($jour, $this->zora, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }

        $this->assertEquals(
            $datePresence,
            $presence->getJour()->getDateJour()->format('Y-m-d'),
            "$datePresence pas trouve 2"
        );

        $ordre = $this->ordreService->getOrdre($presence);

        $this->assertEquals(1, $ordre);
    }

    /**
     * Zora(3) Arwen (3)
     * 1 et 2
     *
     */
    public function testOrdreZoraArwen()
    {
        $datePresence = '2016-11-05';
        $jour = $this->getJour($datePresence);

        if (!$jour) {
            $this->assertNotEmpty($jour);
        }

        $presence = $this->getPresence($jour, $this->zora, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }

        $this->assertEquals(
            $datePresence,
            $presence->getJour()->getDateJour()->format('Y-m-d'),
            "$datePresence pas trouve 2"
        );

        $ordre = $this->ordreService->getOrdre($presence);

        $this->assertEquals(2, $ordre);

        $presence = $this->getPresence($jour, $this->arwen, $this->philippe);
        if (!$presence) {
            return;
        }

        $this->assertEquals(
            $datePresence,
            $presence->getJour()->getDateJour()->format('Y-m-d'),
            "$datePresence pas trouve 2"
        );

        $ordre = $this->ordreService->getOrdre($presence);

        $this->assertEquals(1, $ordre);
    }

    /**
     * Marie(1) et Lisa(2) le 2016-11-08
     * Lisa doit valoir 2
     */
    public function testOrdreMarieLisa()
    {
        $datePresence = '2016-11-08';
        $jour = $this->getJour($datePresence);

        if (!$jour) {
            $this->assertNotEmpty($jour);
        }

        $presence = $this->getPresence($jour, $this->marie, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }
        $ordre = $this->ordreService->getOrdre($presence);
        $this->assertEquals(1, $ordre);

        $presence = $this->getPresence($jour, $this->lisa, $this->philippe);
        if (!$presence) {
            $this->assertNotEmpty($presence);
        }

        $ordre = $this->ordreService->getOrdre($presence);
        $this->assertEquals(2, $ordre);
    }

    /**
     * @param $datePresence
     * @return Jour|null
     */
    public function getJour($datePresence)
    {
        $date = new \DateTime($datePresence);
        $jour = $this->jourRepository->findOneBy(['date_jour' => $date]);

        if (!$jour) {
            $this->assertEquals($datePresence, 'Pas ok', "$datePresence pas trouve 1");
        }

        return $jour;
    }

    /**
     * Retoure une presence suivant le jour, l'enfant et le tuteur
     * @param Jour $jour
     * @param Enfant $enfant
     * @param Tuteur $tuteur
     * @return Presence|boolean
     */
    public function getPresence(Jour $jour, Enfant $enfant, Tuteur $tuteur)
    {
        $presence = $this->presenceRepository->findOneBy(
            array('jour' => $jour, 'enfant' => $enfant, 'tuteur' => $tuteur)
        );
        if ($presence) {
            return $presence;
        }

        $this->assertEquals(
            $jour->getDateJour()->format('Y-m-d'),
            $presence->getJour()->getDateJour()->format('Y-m-d'),
            "$jour pas trouve "
        );

        return false;
    }

}
