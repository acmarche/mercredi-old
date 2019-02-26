<?php

namespace AcMarche\Mercredi\Plaine\Entity;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Jour;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

/**
 * Plaine
 *
 * @ORM\Table("plaine")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineRepository")
 */
class Plaine
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
     * @var string|null $slugname
     * @Gedmo\Slug(fields={"intitule"}, separator="_")
     * @ORM\Column(length=62, unique=true)
     */
    private $slugname;

    /**
     * @var string|null $intitule
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Length(min=3)
     * @Assert\NotBlank()
     */
    private $intitule;

    /**
     * @var float|null $prix1
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $prix1;

    /**
     * @var float|null $prix2
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $prix2;

    /**
     * @var float|null $prix3
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $prix3;

    /**
     * @var string|null $remarques
     * @ORM\Column(type="text", nullable=true)
     */
    protected $remarques;

    /**
     * @var boolean $premat
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    private $premat = false;

    /**
     * @var PlaineJour[]|null $jours
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlaineJour", mappedBy="plaine", cascade={"persist", "remove"})
     * @ORM\OrderBy({"date_jour" = "ASC"})
     */
    protected $jours;

    /**
     * @var Enfant[]|null $enfants
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlaineEnfant", mappedBy="plaine", cascade={"remove"})
     *
     */
    private $enfants;

    /**
     * @var AnimateurPlaine[]|null $animateurs
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\AnimateurPlaine", mappedBy="plaine", cascade={"remove"})
     *
     */
    private $animateurs;

    /**
     * @var PlaineMax[]|null $max
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlaineMax", mappedBy="plaine", cascade={"remove","persist"})
     *
     */
    private $max;

    /**
     * @var boolean $archive
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    private $archive = false;

    /**
     * @var boolean $inscription_ouverture
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    private $inscription_ouverture = false;

    /**
     * @var User|null $user_add
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Security\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user_add;

    /**
     * @var \DateTime|null $created
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime|null $updated
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

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getSlugname(): ?string
    {
        return $this->slugname;
    }

    /**
     * @param null|string $slugname
     */
    public function setSlugname(?string $slugname): void
    {
        $this->slugname = $slugname;
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
     * @return float|null
     */
    public function getPrix1(): ?float
    {
        return $this->prix1;
    }

    /**
     * @param float|null $prix1
     */
    public function setPrix1(?float $prix1): void
    {
        $this->prix1 = $prix1;
    }

    /**
     * @return float|null
     */
    public function getPrix2(): ?float
    {
        return $this->prix2;
    }

    /**
     * @param float|null $prix2
     */
    public function setPrix2(?float $prix2): void
    {
        $this->prix2 = $prix2;
    }

    /**
     * @return float|null
     */
    public function getPrix3(): ?float
    {
        return $this->prix3;
    }

    /**
     * @param float|null $prix3
     */
    public function setPrix3(?float $prix3): void
    {
        $this->prix3 = $prix3;
    }

    /**
     * @return null|string
     */
    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    /**
     * @param null|string $remarques
     */
    public function setRemarques(?string $remarques): void
    {
        $this->remarques = $remarques;
    }

    /**
     * @return bool
     */
    public function isPremat(): bool
    {
        return $this->premat;
    }

    /**
     * @param bool $premat
     */
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

    /**
     * @return bool
     */
    public function isArchive(): bool
    {
        return $this->archive;
    }

    /**
     * @param bool $archive
     */
    public function setArchive(bool $archive): void
    {
        $this->archive = $archive;
    }

    /**
     * @return bool
     */
    public function isInscriptionOuverture(): bool
    {
        return $this->inscription_ouverture;
    }

    /**
     * @param bool $inscription_ouverture
     */
    public function setInscriptionOuverture(bool $inscription_ouverture): void
    {
        $this->inscription_ouverture = $inscription_ouverture;
    }

    /**
     * @return User|null
     */
    public function getUserAdd(): ?User
    {
        return $this->user_add;
    }

    /**
     * @param User|null $user_add
     */
    public function setUserAdd(?User $user_add): void
    {
        $this->user_add = $user_add;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime|null $created
     */
    public function setCreated(?\DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime|null $updated
     */
    public function setUpdated(?\DateTime $updated): void
    {
        $this->updated = $updated;
    }

}
