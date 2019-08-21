<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Security\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Presence
 *
 * @ORM\Table("jour")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\JourRepository")
 * @UniqueEntity("date_jour")
 *
 */
class Jour
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_jour", type="date", unique=true)
     *
     */
    protected $date_jour;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     *
     *
     */
    protected $prix1;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $prix2;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $prix3;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     */
    protected $color;

    /**
     * @var integer
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    protected $archive = false;

    /**
     * @ORM\OneToMany(targetEntity="Presence", mappedBy="jour", cascade={"persist", "remove"})
     *
     */
    protected $presences;

    /**
     * @ORM\ManyToMany(targetEntity="Animateur", mappedBy="jours")
     *
     */
    protected $animateurs;

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

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     */
    protected $remarques;

    public function __construct()
    {
        $this->presences = new ArrayCollection();
        $this->animateurs = new ArrayCollection();
    }

    public function __toString()
    {
        $date_jour = $this->getDateJour();
        if ($date_jour instanceof \DateTime) {
            $jour = $date_jour->format("D");
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

            return $date_jour->format("d-m-Y").' '.$jourFr;
        }

        return '';

    }

    public function getPrixByOrdre($ordre)
    {
        switch ($ordre) {
            case 2:
                return $this->getPrix2();
            case 3:
                return $this->getPrix3();
            default:
                return $this->getPrix1();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateJour(): ?\DateTimeInterface
    {
        return $this->date_jour;
    }

    public function setDateJour(\DateTimeInterface $date_jour): self
    {
        $this->date_jour = $date_jour;

        return $this;
    }

    public function getPrix1(): ?string
    {
        return $this->prix1;
    }

    public function setPrix1(string $prix1): self
    {
        $this->prix1 = $prix1;

        return $this;
    }

    public function getPrix2(): ?string
    {
        return $this->prix2;
    }

    public function setPrix2(string $prix2): self
    {
        $this->prix2 = $prix2;

        return $this;
    }

    public function getPrix3(): ?string
    {
        return $this->prix3;
    }

    public function setPrix3(string $prix3): self
    {
        $this->prix3 = $prix3;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(bool $archive): self
    {
        $this->archive = $archive;

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

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): self
    {
        $this->remarques = $remarques;

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
            $presence->setJour($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
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
     * @return Collection|Animateur[]
     */
    public function getAnimateurs(): Collection
    {
        return $this->animateurs;
    }

    public function addAnimateur(Animateur $animateur): self
    {
        if (!$this->animateurs->contains($animateur)) {
            $this->animateurs[] = $animateur;
            $animateur->addJour($this);
        }

        return $this;
    }

    public function removeAnimateur(Animateur $animateur): self
    {
        if ($this->animateurs->contains($animateur)) {
            $this->animateurs->removeElement($animateur);
            $animateur->removeJour($this);
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
