<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 2/09/17
 * Time: 17:17.
 */

namespace AcMarche\Mercredi\Admin\Tests;

use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Note;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Entity\PlaineJour;
use AcMarche\Mercredi\Security\Entity\Group;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseUnit extends WebTestCase
{
    /**
     * @var Client
     */
    protected $admin;
    /**
     * @var Client
     */
    protected $animateur;
    /**
     * @var Client
     */
    protected $ecole;
    /**
     * @var Client
     */
    protected $parent;
    /**
     * @var Client
     */
    protected $read;
    /**
     * @var Client
     */
    protected $solo;
    /**
     * @var Client
     */
    protected $rleffe;
    /**
     * @var Client
     */
    protected $uharvard;
    /**
     * @var ObjectManager
     */
    protected $em;
    protected $anonyme;

    public function __construct()
    {
        parent::__construct();

        $this->admin = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'jf@marche.be',
                'PHP_AUTH_PW' => 'admin',
            ]
        );
        $this->animateur = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'animateur@marche.be',
                'PHP_AUTH_PW' => 'animateur',
            ]
        );
        $this->parent = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'pmichel@marche.be',
                'PHP_AUTH_PW' => 'admin',
            ]
        );
        $this->ecole = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'ecole@marche.be',
                'PHP_AUTH_PW' => 'ecole',
            ]
        );
        $this->read = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'read@marche.be',
                'PHP_AUTH_PW' => 'read',
            ]
        );
        $this->solo = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'solo@marche.be',
                'PHP_AUTH_PW' => 'homer123',
            ]
        );
        $this->rleffe = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'rleffe@marche.be',
                'PHP_AUTH_PW' => 'homer123',
            ]
        );
        $this->uharvard = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'uharvard@marche.be',
                'PHP_AUTH_PW' => 'homer123',
            ]
        );

        $this->anonyme = static::createClient();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @param $url
     * @param $codeAttendu
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function executeUrl($url, Client $user, $codeAttendu)
    {
        $crawler = $user->request('GET', $url);
        $code = $user->getResponse()->getStatusCode();

        if (404 == $code) {
            // var_dump($url);
        }

        $this->assertEquals($codeAttendu, $code);

        return $crawler;
    }

    /**
     * @param $args
     *
     * @return bool|Enfant|null
     */
    protected function getEnfant($args)
    {
        $enfant = $this->em->getRepository(Enfant::class)->findOneBy(
            $args
        );

        if (!$enfant) {
            $this->assertEquals(0, 'enfant non trouve');

            return false;
        }

        return $enfant;
    }

    /**
     * @param $args
     *
     * @return bool|Tuteur|null
     */
    protected function getTuteur($args)
    {
        $tuteur = $this->em->getRepository(Tuteur::class)->findOneBy(
            $args
        );

        if (!$tuteur) {
            $this->assertEquals(0, 'tuteur non trouve');

            return false;
        }

        return $tuteur;
    }

    /**
     * @param $args
     *
     * @return bool|Note|null
     */
    protected function getNote($args)
    {
        $note = $this->em->getRepository(Note::class)->findOneBy(
            $args
        );

        if (!$note) {
            $this->assertEquals(0, 'note non trouve');

            return false;
        }

        return $note;
    }

    /**
     * @param $args
     *
     * @return bool|PlaineJour|null
     */
    protected function getPlaineJour($args)
    {
        $plaineJour = $this->em->getRepository(PlaineJour::class)->findOneBy(
            $args
        );

        if (!$plaineJour) {
            $this->assertEquals(0, 'jour non trouve');

            return false;
        }

        return $plaineJour;
    }

    /**
     * @param $nom
     *
     * @return bool|Ecole
     */
    protected function getEcole($nom)
    {
        $commerce = $this->em->getRepository(Ecole::class)->findOneBy(
            ['nom' => $nom]
        );

        if (!$commerce) {
            $this->assertEquals(0, 'ecole non trouve');

            return false;
        }

        return $commerce;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->clear();
        $this->em = null; // avoid memory leaks
    }

    public function dateFilter(\DateTime $date)
    {
        $jour = $date->format('D');
        $jourFr = $this->getFr($jour);

        return $date->format('d-m-Y').' '.$jourFr;
    }

    public function getFr($jour)
    {
        switch ($jour) {
            case 'Mon':
                $jourFr = 'Lundi';
                break;
            case 'Tue':
                $jourFr = 'Mardi';
                break;
            case 'Wed':
                $jourFr = 'Mercredi';
                break;
            case 'Thu':
                $jourFr = 'Jeudi';
                break;
            case 'Fri':
                $jourFr = 'Vendredi';
                break;
            case 'Sat':
                $jourFr = 'Samedi';
                break;
            case 'Sun':
                $jourFr = 'Dimanche';
                break;
            default:
                $jourFr = '';
                break;
        }

        return $jourFr;
    }

    /**
     * @param array $args
     *
     * @return SanteQuestion[]|bool
     */
    protected function getQuestions($args)
    {
        $questions = $this->em->getRepository(SanteQuestion::class)->findBy(
            $args
        );

        if (!$questions) {
            $this->assertEquals(0, 'questions non trouve');

            return false;
        }

        return $questions;
    }

    /**
     * @param $args
     *
     * @return bool|Group|null
     */
    protected function getGroup($args)
    {
        $group = $this->em->getRepository(Group::class)->findOneBy(
            $args
        );

        if (!$group) {
            $this->assertEquals(0, 'group non trouve');

            return false;
        }

        return $group;
    }

    /**
     * @param $args
     *
     * @return bool|User|null
     */
    protected function getUser($args)
    {
        $group = $this->em->getRepository(User::class)->findOneBy(
            $args
        );

        if (!$group) {
            $this->assertEquals(0, 'user non trouve');

            return false;
        }

        return $group;
    }
}
