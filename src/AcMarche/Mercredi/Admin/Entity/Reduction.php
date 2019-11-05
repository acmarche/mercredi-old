<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reduction.
 *
 * @ORM\Table("reduction")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\ReductionRepository")
 */
class Reduction
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=120, nullable=false)
     * @Assert\NotBlank()
     */
    protected $nom;

    /**
     * @Gedmo\Slug(fields={"nom"}, separator="_")
     * @ORM\Column(length=62, unique=true)
     */
    protected $slugname;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=false)
     *
     * @Assert\Range(
     *      min = 0,
     *      max = 100
     *     )
     */
    protected $pourcentage;

    /**
     * @ORM\OneToMany(targetEntity="Presence", mappedBy="reduction")
     */
    protected $presence;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $remarques;

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
        $this->presence = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNom().' ('.$this->getPourcentage().'%)';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSlugname(): ?string
    {
        return $this->slugname;
    }

    public function setSlugname(string $slugname): self
    {
        $this->slugname = $slugname;

        return $this;
    }

    public function getPourcentage(): ?float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(float $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

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

    /**
     * @return Collection|Presence[]
     */
    public function getPresence(): Collection
    {
        return $this->presence;
    }

    public function addPresence(Presence $presence): self
    {
        if (!$this->presence->contains($presence)) {
            $this->presence[] = $presence;
            $presence->setReduction($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presence->contains($presence)) {
            $this->presence->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getReduction() === $this) {
                $presence->setReduction(null);
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

    /*
     * STOP
     */
}
