<?php

namespace AcMarche\Mercredi\Parent\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class AssociateControllerTest extends BaseUnit
{
    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/');

        $this->assertGreaterThan(0, $crawler->filter('td:contains("pmichel@marche.be")')->count());
        $crawler = $this->admin->click($crawler->selectLink('pmichel@marche.be')->link());

        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'utilisateur_edit[nom]' => 'Michel',
                'utilisateur_edit[prenom]' => 'Philippe',
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("MICHEL Philippe")')->count());
    }

    public function testDissociate()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/');

        $this->assertGreaterThan(0, $crawler->filter('td:contains("pmichel@marche.be")')->count());
        $crawler = $this->admin->click($crawler->selectLink('pmichel@marche.be')->link());

        $crawler = $this->admin->click($crawler->selectLink('Associer un parent')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array()
        );

        $form['associate_parent[dissocier]'] = 1;

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'div:contains("L\'utilisateur a bien été dissocié.")'
            )->count()
        );
    }

    public function testAssociate()
    {
        $crawler = $this->admin->request('GET', '/security/utilisateurs/');

        $this->assertGreaterThan(0, $crawler->filter('td:contains("pmichel@marche.be")')->count());
        $crawler = $this->admin->click($crawler->selectLink('pmichel@marche.be')->link());

        $crawler = $this->admin->click($crawler->selectLink('Associer un parent')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array()
        );

        $option = $crawler->filter('#associate_parent_tuteur option:contains("MICHEL Philippe")');
        $this->assertEquals(1, count($option), 'MICHEL Philippe non trouvée');
        $value = $option->attr('value');
        $form['associate_parent[tuteur]']->select($value);

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(
            0,
            $crawler->filter('td:contains("MICHEL Philippe")')->count(),
            'Missing element td:contains("MICHEL Philippe")'
        );
    }
}
