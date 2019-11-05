<?php

namespace AcMarche\Mercredi\Plaine\Tests\Controller;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Tests\BaseUnit;
use AcMarche\Mercredi\Plaine\Entity\Plaine;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Plaine\Entity\PlainePresence;

class PzlainePaiementControllerTest extends BaseUnit
{
    private $slugEnfant = 'leruth_timeo';
    private $nomEnfant = 'LERUTH Timeo';
    private $slugParent = 'leruth_nat';
    private $montant = '50.50'; //to disable paiment
    private $slugPlaine = 'carnaval_2020';

    /**
     * ajout paiement.
     */
    private $paiementMontant = '55,26';
    private $paiementMontantTxt = '55.26';
    private $paiementDate = '24/10/2020';
    private $paiementType = 'Plaine';
    private $paiementLink = 'Plaine du 24-10-2020 (55.26 €)';

    private $dateSearch = '2020-10-10';
    private $dateSearch2 = '2020-10-11';

    public function testDisabledCheque()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/'.$this->slugParent);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink("d'un montant de $this->montant €")->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());
        $form = $crawler->selectButton('Mettre à jour')->form([]);

        $form['paiement[cloture]']->tick();

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();
    }

    public function testSansCheque()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/'.$this->slugPlaine);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink($this->nomEnfant)->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $link = $crawler->selectLink('Payer')->first();
        $attr = $link->attr('disabled');

        $this->assertEquals('disabled', $attr);
    }

    public function testAddChequeNat()
    {
        $crawler = $this->admin->request('GET', '/admin/tuteur/'.$this->slugParent);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ajouter un paiement')->link());

        $form = $crawler->selectButton('Ajouter')->form(
            [
                'paiement[montant]' => $this->paiementMontant,
                'paiement[date_paiement]' => $this->paiementDate,
                'paiement[type_paiement]' => $this->paiementType,
            ]
        );

        $option = $crawler->filter('#paiement_enfant option:contains("'.$this->nomEnfant.'")');
        $this->assertEquals(1, count($option));
        $value = $option->attr('value');
        $form['paiement[enfant]']->select($value);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("'.$this->paiementMontantTxt.' €")')->count());
    }

    public function testPayer()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/'.$this->slugPlaine);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink($this->nomEnfant)->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink($this->paiementLink)->link());

        $date = $this->getPresences($this->dateSearch);

        $form = $crawler->selectButton('Valider le paiement')->form([]);
        $form['plaine_presence_paiement[plaine_presences][0]'] = $date;

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->paiementLink.'")')->count());
    }

    public function testEditPayer()
    {
        $crawler = $this->admin->request('GET', '/plaine/plaine/'.$this->slugPlaine);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink($this->nomEnfant)->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Editer le paiement')->link());

        $date = $this->getPresences($this->dateSearch2);

        $form = $crawler->selectButton('Valider le paiement')->form([]);
        $form['plaine_presence_paiement[plaine_presences][0]']->untick();
        $form['plaine_presence_paiement[plaine_presences][1]'] = $date;

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('td:contains("'.$this->paiementLink.'")')->count());
    }

    private function getPresences($date)
    {
        $enfant = $this->em->getRepository(Enfant::class)->findOneBy(['slugname' => $this->slugEnfant]);
        $plaine = $this->em->getRepository(Plaine::class)->findOneBy(['slugname' => $this->slugPlaine]);
        $jour = $this->em->getRepository(PlaineJour::class)->findOneBy(['date_jour' => new \DateTime($date)]);

        $plaineEnfant = $this->em->getRepository(PlaineEnfant::class)->findOneBy(
            [
                'enfant' => $enfant->getId(),
                'plaine' => $plaine->getId(),
            ]
        );

        $presence = $this->em->getRepository(PlainePresence::class)->findOneBy(
            [
                'plaine_enfant' => $plaineEnfant,
                'jour' => $jour->getId(),
            ]
        );

        if ($presence) {
            return $presence->getId();
        }

        return 0;
    }
}
