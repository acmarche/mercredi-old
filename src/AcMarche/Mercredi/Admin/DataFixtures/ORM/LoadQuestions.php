<?php

namespace AcMarche\Mercredi\Admin\DataFixtures\ORM;

use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadQuestions extends Fixture implements ORMFixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        foreach ($this->getDataMedecine() as $order => $question) {
            $this->newQuestion($question, "Informations médicales confidentielles", $order);
        }

        foreach ($this->getDataAutres() as $order => $question) {
            $this->newQuestion($question, "Informations additionnelles", $order);
        }

        $question = $this->newQuestion(
            "Est-il allergique à certains aliments?",
            "Informations additionnelles",
            9
        );
        $question->setComplement(true);
        $question->setComplementLabel('Lesquels');

        $manager->flush();
    }

    /**
     * @param string $intitule
     * @param string $categorie
     * @param $order
     * @return SanteQuestion
     */
    public function newQuestion(string $intitule, string $categorie, $order)
    {
        $question = new SanteQuestion();
        $question->setCategorie($categorie);
        $question->setIntitule($intitule);
        $question->setDisplayOrder($order);
        $this->manager->persist($question);

        return $question;
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDataMedecine()
    {
        return [
            0 => "L'enfant souffre-t-il d'asthme?",
            1 => "L'enfant est-il épileptique?",
            2 => "Souffre-t-il d'une affection cardiaque?",
            3 => "Est-il allergique à certaines matières?",
        ];
    }

    public function getDataAutres()
    {
        return [
            4 => "Photos autorisées ?",
            5 => "Publication des photos par le(s) partenaire(s)",
            6 => "Transport autorisé ?",
        ];
    }

}
