<?php

namespace AcMarche\Mercredi\Parent\Tests;

use AcMarche\Mercredi\Commun\Utils\DateService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ServiceTest extends KernelTestCase
{
    /**
     * @var DateService
     */
    private $dateService;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->dateService = new DateService();
    }

    /**
     * Presence = Admin 21.
     */
    public function testMercredi()
    {
        $jourPresence = \DateTime::createFromFormat('Y-m-d', '2016-09-21');

        //meme jour que presence
        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-09-21 09:10:00');
        $this->assertEquals(false, $this->dateService->checkDate($jourPresence, $today));

        //la veille presence a 9h10
        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-09-20 09:10:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPresence, $today));

        //la veille presence a 10h10
        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-09-20 10:10:00');
        $this->assertEquals(false, $this->dateService->checkDate($jourPresence, $today));

        //la veille presence a 10h02
        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-09-20 10:02:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPresence, $today));

        //a l'avance
        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-09-02 10:12:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPresence, $today));
    }

    public function testMercrediDatePassee()
    {
        $jourPresence = \DateTime::createFromFormat('Y-m-d', '2016-08-17');

        //meme jour que presence
        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-09-21 09:10:00');
        $this->assertEquals(false, $this->dateService->checkDate($jourPresence, $today));
    }

    /**
     * Mardi 20.
     */
    public function testJourneePedagogique()
    {
        $jourPedagogique = \DateTime::createFromFormat('Y-m-d', '2016-09-20');

        //moins de une semaine
        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-09-11 09:10:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPedagogique, $today));

        //une semaine le meme jour
        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-09-13 09:10:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPedagogique, $today));

        //une semaine le jour apres
        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2016-09-14 09:10:00');
        $this->assertEquals(false, $this->dateService->checkDate($jourPedagogique, $today));
    }

    /**
     * Mercredi 08/02/2017.
     */
    public function testMercredi17()
    {
        $jourPedagogique = \DateTime::createFromFormat('Y-m-d', '2017-02-08');

        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-31 09:10:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPedagogique, $today));

        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-31 09:50:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPedagogique, $today));

        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-31 11:50:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPedagogique, $today));

        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-31 12:00:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPedagogique, $today));

        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-31 12:10:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPedagogique, $today));

        $today = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-01-31 13:50:00');
        $this->assertEquals(true, $this->dateService->checkDate($jourPedagogique, $today));
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        //$this->entityManager->close();
    }
}
