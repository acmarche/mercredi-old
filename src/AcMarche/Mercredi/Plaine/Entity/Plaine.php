<?php

namespace AcMarche\Mercredi\Plaine\Entity;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert; // gedmo annotations

/**
 * Plaine.
 *
 * @ORM\Table("plaine")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineRepository")
 */
class Plaine
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
     * @var string|null
     * @Gedmo\Slug(fields={"intitule"}, separator="_")
     * @ORM\Column(length=62, unique=true)
     */
    private $slugname;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Length(min=3)
     * @Assert\NotBlank()
     */
    private $intitule;

    /**
     * @var float|null
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    protected $prix1;

    /**
     * @var float|null
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    protected $prix2;

    /**
     * @var float|null
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    protected $prix3;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    protected $remarques;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     */
    private $premat = false;

    /**
     * @var PlaineJour[]|null
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlaineJour", mappedBy="plaine", cascade={"persist", "remove"})
     * @ORM\OrderBy({"date_jour" = "ASC"})
     */
    protected $jours;

    /**
     * @var Enfant[]|null
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlaineEnfant", mappedBy="plaine", cascade={"remove"})
     */
    private $enfants;

    /**
     * @var AnimateurPlaine[]|null
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\AnimateurPlaine", mappedBy="plaine", cascade={"remove"})
     */
    private $animateurs;

    /**
     * @var PlaineMax[]|null
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlaineMax", mappedBy="plaine", cascade={"remove","persist"})
     */
    private $max;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     */
    private $archive = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     */
    private $inscription_ouverture = false;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Security\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user_add;

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

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->enfants = new ArrayCollection();
        $this->animateurs = new ArrayCollection();
        $this->max = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getIntitule();
    }

    public function getAnnee()
    {
        $first = $this->jours->first();
        if ($first) {
            $date = $first->getDateJour();
            if ($date instanceof \DateTime) {
                return $date->format('Y');
            }
        }

        return null;
    }

    public function addJour(PlaineJour $jour): self
    {
        if (!$this->jours->contains($jour)) {
            $this->jours[] = $jour;
            $jour->setPlaine($this);
        }

        return $this;
    }

    public function removeJour(PlaineJour $jour): self
    {
        if ($this->jours->contains($jour)) {
            $this->jours->removeElement($jour);
            // set the owning side to null (unless already changed)
            if ($jour->getPlaine() === $this) {
                $jour->setPlaine(null);
            }
        }

        return $this;
    }

    public function addEnfant(PlaineEnfant $enfant): self
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->setPlaine($this);
        }

        return $this;
    }

    public function removeEnfant(PlaineEnfant $enfant): self
    {
        if ($this->enfants->contains($enfant)) {
            $this->enfants->removeElement($enfant);
            // set the owning side to null (unless already changed)
            if ($enfant->getPlaine() === $this) {
                $enfant->setPlaine(null);
            }
        }

        return $this;
    }

    public function addAnimateur(AnimateurPlaine $animateur): self
    {
        if (!$this->animateurs->contains($animateur)) {
            $this->animateurs[] = $animateur;
            $animateur->setPlaine($this);
        }

        return $this;
    }

    public function removeAnimateur(AnimateurPlaine $animateur): self
    {
        if ($this->animateurs->contains($animateur)) {
            $this->animateurs->removeElement($animateur);
            // set the owning side to null (unless already changed)
            if ($animateur->getPlaine() === $this) {
                $animateur->setPlaine(null);
            }
        }

        return $this;
    }

    public function addMax(PlaineMax $plaineMax): self
    {
        if (!$this->max->contains($plaineMax)) {
            $this->max[] = $plaineMax;
            $plaineMax->setPlaine($this);
        }

        return $this;
    }

    public function removeMax(PlaineMax $plaineMax): self
    {
        if ($this->jours->contains($plaineMax)) {
            $this->jours->removeElement($plaineMax);
            // set the owning side to null (unless already changed)
            if ($plaineMax->getPlaine() === $this) {
                $plaineMax->setPlaine(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getSlugname(): ?string
    {
        return $this->slugname;
    }

    public function setSlugname(?string $slugname): void
    {
        $this->slugname = $slugname;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(?string $intitule): void
    {
        $this->intitule = $intitule;
    }

    public function getPrix1(): ?float
    {
        return $this->prix1;
    }

    public function setPrix1(?float $prix1): void
    {
        $this->prix1 = $prix1;
    }

    public function getPrix2(): ?float
    {
        return $this->prix2;
    }

    public function setPrix2(?float $prix2): void
    {
        $this->prix2 = $prix2;
    }

    public function getPrix3(): ?float
    {
        return $this->prix3;
    }

    public function setPrix3(?float $prix3): void
    {
        $this->prix3 = $prix3;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): void
    {
        $this->remarques = $remarques;
    }

    public function isPremat(): bool
    {
        return $this->premat;
    }

    public function setPremat(bool $premat): void
    {
        $this->premat = $premat;
    }

    /**
     * @return Jour[]|null
     */
    public function getJours(): ?Collection
    {
        return $this->jours;
    }

    /**
     * @param Jour[]|null $jours
     */
    public function setJours(?array $jours): void
    {
        $this->jours = $jours;
    }

    /**
     * @return Enfant[]|null
     */
    public function getEnfants(): ?Collection
    {
        return $this->enfants;
    }

    /**
     * @param Enfant[]|null $enfants
     */
    public function setEnfants(?array $enfants): void
    {
        $this->enfants = $enfants;
    }

    /**
     * @return AnimateurPlaine[]|null
     */
    public function getAnimateurs(): ?Collection
    {
        return $this->animateurs;
    }

    /**
     * @param AnimateurPlaine[]|null $animateurs
     */
    public function setAnimateurs(?array $animateurs): void
    {
        $this->animateurs = $animateurs;
    }

    /**
     * @return PlaineMax[]|null
     */
    public function getMax(): ?Collection
    {
        return $this->max;
    }

    /**
     * @param PlaineMax[]|null $max
     */
    public function setMax(?array $max): void
    {
        $this->max = $max;
    }

    public function isArchive(): bool
    {
        return $this->archive;
    }

    public function setArchive(bool $archive): void
    {
        $this->archive = $archive;
    }

    public function isInscriptionOuverture(): bool
    {
        return $this->inscription_ouverture;
    }

    public function setInscriptionOuverture(bool $inscription_ouverture): void
    {
        $this->inscription_ouverture = $inscription_ouverture;
    }

    public function getUserAdd(): ?User
    {
        return $this->user_add;
    }

    public function setUserAdd(?User $user_add): void
    {
        $this->user_add = $user_add;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(?\DateTime $created): void
    {
        $this->created = $created;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTime $updated): void
    {
        $this->updated = $updated;
    }
}
