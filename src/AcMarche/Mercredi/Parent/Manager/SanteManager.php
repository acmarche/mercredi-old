<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 23/08/18
 * Time: 14:57
 */

namespace AcMarche\Mercredi\Parent\Manager;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Admin\Entity\Sante\SanteReponse;
use AcMarche\Mercredi\Admin\Repository\EnfantRepository;
use AcMarche\Mercredi\Admin\Repository\SanteFicheRepository;
use AcMarche\Mercredi\Admin\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Admin\Repository\SanteReponseRepository;
use AcMarche\Mercredi\Commun\Utils\SortUtils;

class SanteManager
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var SanteFicheRepository
     */
    private $santeFicheRepository;
    /**
     * @var SanteQuestionRepository
     */
    private $santeQuestionRepository;
    /**
     * @var SanteReponseRepository
     */
    private $santeReponseRepository;
    /**
     * @var SortUtils
     */
    private $sortUtils;

    public function __construct(
        EnfantRepository $enfantRepository,
        SanteFicheRepository $santeFicheRepository,
        SanteQuestionRepository $santeQuestionRepository,
        SanteReponseRepository $santeReponseRepository,
        SortUtils $sortUtils
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->santeFicheRepository = $santeFicheRepository;
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->santeReponseRepository = $santeReponseRepository;
        $this->sortUtils = $sortUtils;
    }

    /**
     * @param Enfant $enfant
     * @return SanteFiche|null
     */
    public function getSanteFiche(Enfant $enfant): SanteFiche
    {
        if (!$santeFiche = $this->santeFicheRepository->findOneBy(['enfant' => $enfant])) {
            $santeFiche = new SanteFiche();
            $santeFiche->setEnfant($enfant);
        }

        return $santeFiche;
    }

    /**
     * @param SanteFiche $santeFiche
     * @return SanteQuestion[]
     */
    public function bindResponses(SanteFiche $santeFiche)
    {
        $questions = $this->getAllQuestions();
        foreach ($questions as $question) {

            $reponse = $this->getSanteReponse($santeFiche, $question);

            if ($reponse instanceof SanteReponse) {
                $reponse->getQuestion();
                $question->setReponse($reponse->getReponse());
                $question->setRemarque($reponse->getRemarque());
            } else {
                $question->setReponse(null);
            }
        }

        return $questions;
    }

    /**
     * @param SanteFiche $santeFiche
     * @return SanteReponse[]
     */
    public function getReponses(SanteFiche $santeFiche)
    {
        return $this->santeReponseRepository->findBy(['sante_fiche' => $santeFiche]);
    }

    /**
     *
     * @return SanteQuestion[]
     */
    public function getAllQuestions()
    {
        return $this->santeQuestionRepository->findAll();
    }

    /**
     * Donne la reponse a une question ou pas
     * @param SanteFiche $santeFiche
     * @param SanteQuestion $santeQuestion
     * @return SanteReponse
     */
    public function getSanteReponse(SanteFiche $santeFiche, SanteQuestion $santeQuestion)
    {
        return $this->santeReponseRepository->findOneBy(
            ['sante_fiche' => $santeFiche, 'question' => $santeQuestion]
        );
    }

    /**
     * Si pas de reponse ou remarque on ne cree pas la reponse
     * @param SanteFiche $santeFiche
     * @param SanteQuestion $santeQuestion
     * @return null|void
     */
    public function handleReponse(SanteFiche $santeFiche, SanteQuestion $santeQuestion)
    {
        $santeReponse = $this->getSanteReponse($santeFiche, $santeQuestion);
        if (!$santeReponse) {
            if ($santeQuestion->getReponse() === null && !$santeQuestion->getRemarque()) {
                return null;
            }
            $santeReponse = $this->createSanteReponse($santeFiche, $santeQuestion);
        }

        $santeReponse->setReponse($santeQuestion->getReponse());
        $santeReponse->setRemarque($santeQuestion->getRemarque());
    }

    /**
     * @param SanteFiche $santeFiche
     * @param SanteQuestion $santeQuestion
     *
     * @return SanteReponse
     */
    public function createSanteReponse(SanteFiche $santeFiche, SanteQuestion $santeQuestion)
    {
        $santeReponse = new SanteReponse();
        $santeReponse->setSanteFiche($santeFiche);
        $santeReponse->setQuestion($santeQuestion);
        $this->santeReponseRepository->insert($santeReponse);

        return $santeReponse;
    }

    /**
     * @todo if else necessaire?
     * @param SanteFiche $santeFiche
     */
    public function saveSanteFiche(SanteFiche $santeFiche)
    {
        if (!$santeFiche->getId()) {
            $this->santeFicheRepository->insert($santeFiche);
        } else {
            $santeFiche->setUpdatedAt(new \DateTime());
            $this->santeFicheRepository->save();
        }
    }

    public function Reponsesflush()
    {
        $this->santeReponseRepository->save();
    }

    /**
     * @param Enfant $enfant
     * @return bool
     */
    public function isComplete(Enfant $enfant): bool
    {
        $santeFiche = $this->getSanteFiche($enfant);
        $reponses = $this->getReponses($santeFiche);
        $questions = $this->getAllQuestions();

        if (count($reponses) < count($questions)) {
            return false;
        }

        foreach ($reponses as $reponse) {
            $question = $reponse->getQuestion();
            if (!$this->checkQuestionOk($question)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param SanteQuestion $question
     * @return bool
     */
    public function checkQuestionOk(SanteQuestion $question)
    {
        if ($question->getComplement()) {
            if ($question->getReponse()) {
                if (trim($question->getRemarque() == '')) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param Enfant[] $enfants
     */
    public function isCompleteForEnfants(array $enfants)
    {
        foreach ($enfants as $enfant) {
            if ($this->isComplete($enfant)) {
                $enfant->setSanteFicheComplete(true);
            }
        }
    }
}