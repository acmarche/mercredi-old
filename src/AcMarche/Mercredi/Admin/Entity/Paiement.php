<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Plaine\Entity\PlainePresence;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Presence
 *
 * @ORM\Table("paiement")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\PaiementRepository")
 *
 */
class Paiement
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
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $montant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=false)
     *
     */
    protected $date_paiement;

    /**
     * @ORM\Column(type="string", nullable=true, length=150)
     * @var string?
     *
     */
    protected $type_paiement;

    /**
     * @ORM\Column(type="string", nullable=true, length=150)
     * @var string?
     *
     */
    protected $mode_paiement;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", length=2, nullable=true, options={"comment" = "1,2, suviant", "default" = "0"})
     *
     */
    protected $ordre = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default" = "0"})
     *
     */
    protected $cloture = false;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     */
    protected $remarques;

    /**
     * @ORM\ManyToOne(targetEntity="Tuteur", inversedBy="paiements")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $tuteur;

    /**
     * @ORM\ManyToOne(targetEntity="Enfant", inversedBy="paiements")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $enfant;

    /**
     * @ORM\OneToMany(targetEntity="Presence", mappedBy="paiement")
     *
     */
    protected $presences;

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlainePresence", mappedBy="paiement")
     *
     */
    protected $plaine_presences;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Security\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user_add;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated;

    public function __construct()
    {
        $this->presences = new ArrayCollection();
        $this->plaine_presences = new ArrayCollection();
    }

    public function __toString()
    {
        $string = $this->getTypePaiement().' du ';
        if ($this->getDatePaiement()) {
            $string .= $this->getDatePaiement()->format('d-m-Y');
        }
        $string .= ' ('.$this->getMontant().' €)';

        return $string;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(\DateTimeInterface $date_paiement): self
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getTypePaiement(): ?string
    {
        return $this->type_paiement;
    }

    public function setTypePaiement(?string $type_paiement): self
    {
        $this->type_paiement = $type_paiement;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->mode_paiement;
    }

    public function setModePaiement(?string $mode_paiement): self
    {
        $this->mode_paiement = $mode_paiement;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getCloture(): ?bool
    {
        return $this->cloture;
    }

    public function setCloture(bool $cloture): self
    {
        $this->cloture = $cloture;

        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): self
    {
        $this->remarques = $remarques;

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

    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function setTuteur(?Tuteur $tuteur): self
    {
        $this->tuteur = $tuteur;

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
     * @return Collection|Presence[]
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): self
    {
        if (!$this->presences->contains($presence)) {
            $this->presences[] = $presence;
            $presence->setPaiement($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getPaiement() === $this) {
                $presence->setPaiement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlainePresence[]
     */
    public function getPlainePresences(): Collection
    {
        return $this->plaine_presences;
    }

    public function addPlainePresence(PlainePresence $plainePresence): self
    {
        if (!$this->plaine_presences->contains($plainePresence)) {
            $this->plaine_presences[] = $plainePresence;
            $plainePresence->setPaiement($this);
        }

        return $this;
    }

    public function removePlainePresence(PlainePresence $plainePresence): self
    {
        if ($this->plaine_presences->contains($plainePresence)) {
            $this->plaine_presences->removeElement($plainePresence);
            // set the owning side to null (unless already changed)
            if ($plainePresence->getPaiement() === $this) {
                $plainePresence->setPaiement(null);
            }
        }

        return $this;
    }

    public function getUserAdd(): ?User
    {
        return $this->user_add;
    }

    public function setUserAdd(?User $user_add): self
    {
        $this->user_add = $user_add;

        return $this;
    }

    /**
     * STOP
     */
}
