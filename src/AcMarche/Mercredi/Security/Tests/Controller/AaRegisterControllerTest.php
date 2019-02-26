<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 21/08/18
 * Time: 15:59
 */

namespace AcMarche\Mercredi\Security\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class AaRegisterControllerTest extends BaseUnit
{
    public function testNewParent()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Ajouter")->form(
            array(
                'utilisateur[nom]' => 'Leffe',
                'utilisateur[prenom]' => 'Leffe',
                'utilisateur[email]' => 'rleffe@marche.be',
            )
        );

        $groupParent = $this->getGroup(['name' => 'MERCREDI_PARENT']);

        $form['utilisateur[groups][1]'] = $groupParent->getId();

        $this->admin->submit($form);

        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("L\'utilisateur a bien été ajouté")')->count());

        $this->assertGreaterThan(0, $crawler->filter('td:contains("MERCREDI_PARENT")')->count());
    }

    public function testNewAnimateur()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Ajouter")->form(
            array(
                'utilisateur[nom]' => 'Ducobu',
                'utilisateur[prenom]' => 'Fred',
                'utilisateur[email]' => 'ducobu@marche.be',
            )
        );

        $group = $this->getGroup(['name' => 'MERCREDI_ANIMATEUR']);

        $form['utilisateur[groups][2]'] = $group->getId();

        $this->admin->submit($form);

        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("L\'utilisateur a bien été ajouté")')->count());

        $this->assertGreaterThan(0, $crawler->filter('td:contains("MERCREDI_ANIMATEUR")')->count());
    }

    public function testNewEcole()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Ajouter")->form(
            array(
                'utilisateur[nom]' => 'Désiré',
                'utilisateur[prenom]' => 'Mégo',
                'utilisateur[email]' => 'uharvard@marche.be',
            )
        );

        $group = $this->getGroup(['name' => 'MERCREDI_ECOLE']);

        $form['utilisateur[groups][3]'] = $group->getId();

        $this->admin->submit($form);

        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("L\'utilisateur a bien été ajouté")')->count());

        $this->assertGreaterThan(0, $crawler->filter('td:contains("MERCREDI_ECOLE")')->count());
    }

    public function testNewMultiCompte()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Ajouter")->form(
            array(
                'utilisateur[nom]' => 'Multi',
                'utilisateur[prenom]' => 'Aria',
                'utilisateur[email]' => 'aria@marche.be',
            )
        );

        $admin = $this->getGroup(['name' => 'MERCREDI_ADMIN']);
        $parent = $this->getGroup(['name' => 'MERCREDI_PARENT']);
        $animateur = $this->getGroup(['name' => 'MERCREDI_ANIMATEUR']);
        $form['utilisateur[groups][0]'] = $admin->getId();
        $form['utilisateur[groups][1]'] = $parent->getId();
        $form['utilisateur[groups][2]'] = $animateur->getId();

        $this->admin->submit($form);

        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('div:contains("L\'utilisateur a bien été ajouté")')->count());

        $this->assertGreaterThan(0, $crawler->filter('td:contains("MERCREDI_ADMIN")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("MERCREDI_PARENT")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("MERCREDI_ANIMATEUR")')->count());
    }

    public function testQuickWithCompteExist()
    {
        $crawler = $this->admin->request('GET', '/admin/quick/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();

        $ecole = $this->getEcole("Saint-Martin");

        $form["quick[tuteur][nom]"] = "Lambert";
        $form["quick[tuteur][prenom]"] = "Joseph";
        $form["quick[tuteur][adresse]"] = "Rue de Francorchamps";
        $form["quick[tuteur][code_postal]"] = 6900;
        $form["quick[tuteur][localite]"] = "Champlon";
        $form["quick[tuteur][telephone]"] = "084 56 98 74";
        $form["quick[enfant][nom]"] = "Lambert";
        $form["quick[enfant][prenom]"] = "John";
        $form["quick[tuteur][email]"] = "rleffe@marche.be";
        $form["quick[enfant][birthday][day]"] = 13;
        $form["quick[enfant][birthday][month]"] = 11;
        $form["quick[enfant][birthday][year]"] = 2015;
        $form["quick[enfant][annee_scolaire]"] = "1P";
        $form["quick[enfant][ecole]"] = $ecole->getId();

        $crawler = $this->admin->submit($form);

        $this->assertGreaterThan(0, $crawler->filter('div:contains("La fiche parent LAMBERT Joseph")')->count());
        $this->assertGreaterThan(0, $crawler->filter('div:contains("La fiche enfant LAMBERT John")')->count());
    }


}