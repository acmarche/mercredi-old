<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class PyaerTest extends BaseUnit
{
    private $urlLisa = 'michel_lisa';

    /**
     * Je paie pour cette date.
     */
    private $date = '2016-11-01';
    private $dateCheck = '01-11-2016 Mardi';

    /**
     * Puis je paie cette date mais en decochant la premiere.
     */
    private $date2 = '2016-11-05';
    private $date2Check = '  05-11-2016 Samedi';

    private $lisaMontant = '51.32';
    private $slugNatacha = 'sion_natacha';

    public function testPayer()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->urlLisa);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink($this->lisaMontant.' €')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Payer des présences')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Mettre à jour')->form([]);

        $presence = $this->getPresences($this->date);
        $form['payer[presences][0]'] = $presence;

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->dateCheck.'")')->count());
    }

    public function testEditPayer()
    {
        $crawler = $this->admin->request('GET', '/admin/enfant/'.$this->urlLisa);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink($this->lisaMontant.' €')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Payer des présences')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Mettre à jour')->form([]);

        $presence = $this->getPresences($this->date2);
        $form['payer[presences][0]']->untick();
        $form['payer[presences][1]'] = $presence;

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->date2Check.'")')->count());
    }

    private function getPresences($date)
    {
        $jour = $this->em->getRepository(Jour::class)->findOneBy(['date_jour' => new \DateTime($date)]);
        $enfant = $this->em->getRepository(Enfant::class)->findOneBy(['slugname' => $this->urlLisa]);
        $tuteur = $this->em->getRepository(Tuteur::class)->findOneBy(['slugname' => $this->slugNatacha]);

        $presence = $this->em->getRepository(Presence::class)->search(
            [
                'enfant' => $enfant,
                'tuteur' => $tuteur,
                'jour' => $jour,
            ]
        );

        if (count($presence)) {
            return $presence[0]->getId();
        }

        return 0;
    }
}
