<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Security\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

// gedmo annotations

/**
 * Presence
 *
 * @ORM\Table("caisse_allocation")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\CaisseAllocationRepository")
 *
 */
class CaisseAllocation
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $nom;

    /**
     * @Gedmo\Slug(fields={"nom"}, separator="_")
     * @ORM\Column(length=62, unique=true)
     *
     */
    protected $slugname;

    /**
     * @ORM\OneToMany(targetEntity="Tuteur", mappedBy="caisse_allocation")
     *
     */
    protected $tuteur;

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
        $this->tuteur = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNom();
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
     * @return Collection|Tuteur[]
     */
    public function getTuteur(): Collection
    {
        return $this->tuteur;
    }

    public function addTuteur(Tuteur $tuteur): self
    {
        if (!$this->tuteur->contains($tuteur)) {
            $this->tuteur[] = $tuteur;
            $tuteur->setCaisseAllocation($this);
        }

        return $this;
    }

    public function removeTuteur(Tuteur $tuteur): self
    {
        if ($this->tuteur->contains($tuteur)) {
            $this->tuteur->removeElement($tuteur);
            // set the owning side to null (unless already changed)
            if ($tuteur->getCaisseAllocation() === $this) {
                $tuteur->setCaisseAllocation(null);
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
