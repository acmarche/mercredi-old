<?php

namespace AcMarche\Mercredi\Admin\Tests\Controller;

use AcMarche\Mercredi\Admin\Tests\BaseUnit;

class QuestionTest extends BaseUnit
{
    public function testIndex()
    {
        $crawler = $this->admin->request('GET', '/admin/question/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->admin->request('GET', '/admin/question/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'sante_question[intitule]' => 'Votre enfant sait-il nage',
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Votre enfant sait-il nage")')->count());
    }

    public function testEdit()
    {
        $crawler = $this->admin->request('GET', '/admin/question/');
        $crawler = $this->admin->click($crawler->selectLink('Votre enfant sait-il nage')->link());
        $crawler = $this->admin->click($crawler->selectLink('Editer')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            array(
                'sante_question[intitule]' => 'Votre enfant sait-il nager',
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("Votre enfant sait-il nager")')->count());
    }

    public function testDelete()
    {
        $crawler = $this->admin->request('GET', '/admin/question/');
        $crawler = $this->admin->click($crawler->selectLink('Votre enfant sait-il nager')->link());

        $crawler = $this->admin->click($crawler->selectLink('Supprimer')->link());

        $this->admin->submit($crawler->selectButton('Supprimer')->form());
        $this->admin->followRedirect();

        $this->assertEquals(0, $crawler->filter('td:contains("nager")')->count());
    }

    public function testAddWithComplement()
    {
        $crawler = $this->admin->request('GET', '/admin/question/new');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form(
            array(
                'sante_question[intitule]' => "L'enfant est-il atteint de diabète?",
                'sante_question[complement]' => 1,
                'sante_question[complementLabel]' => "Quel type de diabète?",
            )
        );

        $this->admin->submit($form);
        $crawler = $this->admin->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h3:contains("L\'enfant est-il atteint de diabète?")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Quel type de diabète?")')->count());
    }


    public function testTrier()
    {
        $crawler = $this->admin->request('GET', '/admin/question/');
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $crawler = $this->admin->click($crawler->selectLink('Ordre d\'affichage')->link());
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

        $questionsID = [];
        $questions = $this->getQuestions([]);
        foreach ($questions as $question) {
            $questionsID[] = $question->getId();
        }

        $questions = ['questions' => $questionsID];

        $crawler = $this->admin->request('POST', '/admin/question/trier', $questions);
        $this->assertEquals(200, $this->admin->getResponse()->getStatusCode());

    }
}
