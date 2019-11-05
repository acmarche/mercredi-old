<?php

namespace AcMarche\Mercredi\Admin\Entity\Sante;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("sante_reponse")
 * @ORM\Entity()
 */
class SanteReponse
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var SanteQuestion
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Sante\SanteQuestion" )
     */
    protected $question;

    /**
     * @var SanteFiche
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Sante\SanteFiche", inversedBy="reponses", cascade={"remove"})
     */
    protected $sante_fiche;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $reponse;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $remarque;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?SanteQuestion
    {
        return $this->question;
    }

    public function setQuestion(?SanteQuestion $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getSanteFiche(): ?SanteFiche
    {
        return $this->sante_fiche;
    }

    public function setSanteFiche(?SanteFiche $sante_fiche): self
    {
        $this->sante_fiche = $sante_fiche;

        return $this;
    }

    public function getReponse(): ?bool
    {
        return $this->reponse;
    }

    public function setReponse(?bool $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(?string $remarque): self
    {
        $this->remarque = $remarque;

        return $this;
    }
}
