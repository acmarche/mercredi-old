<?php

namespace AcMarche\Mercredi\Admin\Entity\Sante;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table("sante_reponse")
 * @ORM\Entity()
 *
 */
class SanteReponse
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id;

    /**
     * @var SanteQuestion $question
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion" )
     *
     */
    protected $question;

    /**
     * @var SanteFiche $sante_fiche
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Sante\SanteFiche", inversedBy="reponses", cascade={"remove"})
     *
     */
    protected $sante_fiche;

    /**
     * @var boolean|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     *
     */
    protected $reponse;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $remarque;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return SanteQuestion
     */
    public function getQuestion(): SanteQuestion
    {
        return $this->question;
    }

    /**
     * @param SanteQuestion $question
     */
    public function setQuestion(SanteQuestion $question): void
    {
        $this->question = $question;
    }

    /**
     * @return SanteFiche
     */
    public function getSanteFiche(): SanteFiche
    {
        return $this->sante_fiche;
    }

    /**
     * @param SanteFiche $sante_fiche
     */
    public function setSanteFiche(SanteFiche $sante_fiche): void
    {
        $this->sante_fiche = $sante_fiche;
    }

    /**
     * @return bool|null
     */
    public function getReponse(): ?bool
    {
        return $this->reponse;
    }

    /**
     * @param bool|null $reponse
     */
    public function setReponse(?bool $reponse): void
    {
        $this->reponse = $reponse;
    }

    /**
     * @return null|string
     */
    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    /**
     * @param null|string $remarque
     */
    public function setRemarque(?string $remarque): void
    {
        $this->remarque = $remarque;
    }

}
