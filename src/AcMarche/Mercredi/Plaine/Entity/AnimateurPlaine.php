<?php

namespace AcMarche\Mercredi\Plaine\Entity;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * AnimateurPlaine
 *
 * @ORM\Table("animateur_plaine", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"animateur_id", "plaine_id"})
 * })))
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\AnimateurPlaineRepository")
 * @UniqueEntity(fields={"animateur","plaine"}, message="add.animateur.plaine")
 *
 */
class AnimateurPlaine
{
    /**
     * @var integer|null $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Animateur|null $animateur
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Animateur", inversedBy="plaines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $animateur;

    /**
     * @var Plaine|null $plaine
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Plaine\Entity\Plaine", inversedBy="animateurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plaine;

    /**
     * @var PlaineJour[]|null $jours
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlaineJour", inversedBy="animateurPlaines")
     * @ORM\JoinTable(name="animateur_plaine_jours")
     */
    private $jours;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnimateur(): ?Animateur
    {
        return $this->animateur;
    }

    public function setAnimateur(?Animateur $animateur): self
    {
        $this->animateur = $animateur;

        return $this;
    }

    public function getPlaine(): ?Plaine
    {
        return $this->plaine;
    }

    public function setPlaine(?Plaine $plaine): self
    {
        $this->plaine = $plaine;

        return $this;
    }

    /**
     * @return Collection|PlaineJour[]
     */
    public function getJours(): Collection
    {
        return $this->jours;
    }

    public function addJour(PlaineJour $jour): self
    {
        if (!$this->jours->contains($jour)) {
            $this->jours[] = $jour;
        }

        return $this;
    }

    public function removeJour(PlaineJour $jour): self
    {
        if ($this->jours->contains($jour)) {
            $this->jours->removeElement($jour);
        }

        return $this;
    }
}
