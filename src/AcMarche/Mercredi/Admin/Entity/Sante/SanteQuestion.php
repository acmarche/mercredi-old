<?php

namespace AcMarche\Mercredi\Admin\Entity\Sante;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("sante_question")
 * @ORM\Entity()
 *
 */
class SanteQuestion
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
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $intitule;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     */
    protected $categorie;

    /**
     * Information complementaire necessaire
     * @var boolean|null
     * @ORM\Column(type="boolean", nullable=true)
     *
     */
    protected $complement;

    /**
     * Texte d'aide pour le complement
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $complement_label;

    /**
     * @var integer|null
     * @ORM\Column(type="integer",nullable=true)
     *
     */
    protected $display_order;

    /**
     * @var integer|null
     * 0 => Non, 1 => Oui, -1 => Pas de reponse
     */
    protected $reponse;

    /**
     * @var string|null
     *
     */
    protected $remarque;

    /**
     * @var SanteFiche $sante_fiche
     */
    protected $sante_fiche;

    public function __construct()
    {

    }

    public function __toString()
    {
        return $this->getIntitule();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    /**
     * @param null|string $intitule
     */
    public function setIntitule(?string $intitule): void
    {
        $this->intitule = $intitule;
    }

    /**
     * @return null|string
     */
    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    /**
     * @param null|string $categorie
     */
    public function setCategorie(?string $categorie): void
    {
        $this->categorie = $categorie;
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

    /**
     * @return int|null
     */
    public function getReponse(): ?int
    {
        return $this->reponse;
    }

    /**
     * @param int|null $reponse
     */
    public function setReponse(?int $reponse): void
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

    /**
     * @return bool|null
     */
    public function getComplement(): ?bool
    {
        return $this->complement;
    }

    /**
     * @param bool|null $complement
     */
    public function setComplement(?bool $complement): void
    {
        $this->complement = $complement;
    }

    /**
     * @return null|string
     */
    public function getComplementLabel(): ?string
    {
        return $this->complement_label;
    }

    /**
     * @param null|string $complement_label
     */
    public function setComplementLabel(?string $complement_label): void
    {
        $this->complement_label = $complement_label;
    }

    /**
     * @return int|null
     */
    public function getDisplayOrder(): ?int
    {
        return $this->display_order;
    }

    /**
     * @param int|null $display_order
     */
    public function setDisplayOrder(?int $display_order): void
    {
        $this->display_order = $display_order;
    }
}
