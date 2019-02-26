<?php

namespace AcMarche\Mercredi\Plaine\Entity;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use AcMarche\Mercredi\Admin\Entity\Tuteur;

/**
 * PlaineEnfant
 *
 * @ORM\Table("plaine_enfant")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineEnfantRepository")
 */

class PlaineEnfant
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Plaine\Entity\Plaine", inversedBy="enfants")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $plaine;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Enfant", inversedBy="plaines")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $enfant;

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlainePresence", mappedBy="plaine_enfant", cascade={"remove"})
     *
     */
    private $presences;

    /**
     * Pour export pdf coordonnees
     * @var Tuteur
     */
    private $tuteur;
    
    /**
     * Pour export pdf coche present ou pas
     * @var array of integer
     */
    private $jour_ids;

    public function __construct()
    {
        $this->presences = new ArrayCollection();
    }

    public function setTuteur(Tuteur $tuteur)
    {
        $this->tuteur = $tuteur;
        return $this;
    }

    public function getTuteur()
    {
        return $this->tuteur;
    }

    public function addJourIds(array $jour_ids)
    {
        $this->jour_ids = $jour_ids;
        return $this;
    }

    public function getJourIds()
    {
        return $this->jour_ids;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }

    public function setEnfant(?Enfant $enfant): self
    {
        $this->enfant = $enfant;

        return $this;
    }

    /**
     * @return Collection|PlainePresence[]
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(PlainePresence $presence): self
    {
        if (!$this->presences->contains($presence)) {
            $this->presences[] = $presence;
            $presence->setPlaineEnfant($this);
        }

        return $this;
    }

    public function removePresence(PlainePresence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getPlaineEnfant() === $this) {
                $presence->setPlaineEnfant(null);
            }
        }

        return $this;
    }

    /**
     * STOP
     */
}
