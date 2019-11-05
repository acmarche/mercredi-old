<?php

namespace AcMarche\Mercredi\Plaine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Jours d'une plaine.
 *
 * @ORM\Table("plaine_jours", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"date_jour", "plaine_id"})
 * }))
 * @UniqueEntity({"date_jour", "plaine"})
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineJourRepository")
 */
class PlaineJour
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_jour", type="date")
     */
    private $date_jour;

    /**
     * @var Plaine|null
     * @ORM\ManyToOne(targetEntity="Plaine", inversedBy="jours")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plaine;

    /**
     * @var PlainePresence[]|null
     * @ORM\OneToMany(targetEntity="PlainePresence", mappedBy="jour", cascade={"remove"})
     */
    private $presences;

    /**
     * @var AnimateurPlaine[]|null
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\AnimateurPlaine", mappedBy="jours")
     */
    private $animateurPlaines;

    /**
     * @var \DateTime|null
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * pour compter le nbre d enfants par jour.
     *
     * @var int|null
     */
    private $enfants = 0;
    /**
     * @var int|null
     */
    private $enfantsMoins6 = 0;

    public function __construct()
    {
        $this->presences = new ArrayCollection();
        $this->animateurPlaines = new ArrayCollection();
    }

    public function __toString()
    {
        $date_jour = $this->getDateJour();

        if (is_a($date_jour, 'DateTime')) {
            $jour = $date_jour->format('D');
            switch ($jour) {
                case 'Mon':
                    $jourFr = 'Lundi';
                    break;
                case 'Tue':
                    $jourFr = 'Mardi';
                    break;
                case 'Wed':
                    $jourFr = 'Admin';
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

            return $date_jour->format('d-m-Y').' '.$jourFr;
        } else {
            return '';
        }
    }

    public function setEnfants($count)
    {
        $this->enfants = $count;

        return $this;
    }

    public function getEnfants()
    {
        return $this->enfants;
    }

    public function addEnfant()
    {
        ++$this->enfants;
    }

    public function setEnfantsMoins6($count)
    {
        $this->enfantsMoins6 = $count;

        return $this;
    }

    public function getEnfantsMoins6()
    {
        return $this->enfantsMoins6;
    }

    public function addEnfantMoins6()
    {
        ++$this->enfantsMoins6;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateJour(): ?\DateTimeInterface
    {
        return $this->date_jour;
    }

    public function setDateJour(?\DateTimeInterface $date_jour): self
    {
        $this->date_jour = $date_jour;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

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
            $presence->setJour($this);
        }

        return $this;
    }

    public function removePresence(PlainePresence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getJour() === $this) {
                $presence->setJour(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AnimateurPlaine[]
     */
    public function getAnimateurPlaines(): Collection
    {
        return $this->animateurPlaines;
    }

    public function addAnimateurPlaine(AnimateurPlaine $animateurPlaine): self
    {
        if (!$this->animateurPlaines->contains($animateurPlaine)) {
            $this->animateurPlaines[] = $animateurPlaine;
            $animateurPlaine->addJour($this);
        }

        return $this;
    }

    public function removeAnimateurPlaine(AnimateurPlaine $animateurPlaine): self
    {
        if ($this->animateurPlaines->contains($animateurPlaine)) {
            $this->animateurPlaines->removeElement($animateurPlaine);
            $animateurPlaine->removeJour($this);
        }

        return $this;
    }
}
