<?php

namespace AcMarche\Mercredi\Admin\Entity\Sante;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("sante_question")
 * @ORM\Entity()
 */
class SanteQuestion
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
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    protected $intitule;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $categorie;

    /**
     * Information complementaire necessaire.
     *
     * @var bool|null
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $complement;

    /**
     * Texte d'aide pour le complement.
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $complement_label;

    /**
     * @var int|null
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $display_order;

    /**
     * @var int|null
     *               0 => Non, 1 => Oui, -1 => Pas de reponse
     */
    protected $reponse;

    /**
     * @var string|null
     */
    protected $remarque;

    /**
     * @var SanteFiche
     */
    protected $sante_fiche;

    public function __construct()
    {
    }

    public function __toString()
    {
        return $this->getIntitule();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSanteFiche()
    {
        return $this->sante_fiche;
    }

    /**
     * @param mixed $sante_fiche
     */
    public function setSanteFiche($sante_fiche): void
    {
        $this->sante_fiche = $sante_fiche;
    }

    public function getReponse(): ?int
    {
        return $this->reponse;
    }

    public function setReponse(?int $reponse): void
    {
        $this->reponse = $reponse;
    }

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(?string $remarque): void
    {
        $this->remarque = $remarque;
    }

    public function getComplement(): ?bool
    {
        return $this->complement;
    }

    public function setComplement(?bool $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getComplementLabel(): ?string
    {
        return $this->complement_label;
    }

    public function setComplementLabel(?string $complement_label): self
    {
        $this->complement_label = $complement_label;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->display_order;
    }

    public function setDisplayOrder(?int $display_order): self
    {
        $this->display_order = $display_order;

        return $this;
    }
}
