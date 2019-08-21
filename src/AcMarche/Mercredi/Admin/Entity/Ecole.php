<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Security\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ecole
 *
 * @ORM\Table("ecole")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\EcoleRepository")
 *
 */
class Ecole
{
    //use TimestampableEntity;

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
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $adresse;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", length=6, nullable=true)
     *
     */
    protected $code_postal;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $localite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     *
     */
    protected $telephone;

     /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     *
     */
    protected $gsm;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=true)
     *
     *
     */
    protected $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     */
    protected $remarques;

    /**
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Security\Entity\User", mappedBy="ecoles" )
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Admin\Entity\Enfant", mappedBy="ecole")
     * @ORM\OrderBy({"nom" = "ASC"})
     */
    protected $enfants;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->enfants = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * Pour le listing ecole
     * @param $enfants
     * @return $this
     */
    public function setEnfants($enfants)
    {
        $this->enfants = $enfants;

        return $this;
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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection|Enfant[]
     */
    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function addEnfant(Enfant $enfant): self
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->setEcole($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        if ($this->enfants->contains($enfant)) {
            $this->enfants->removeElement($enfant);
            // set the owning side to null (unless already changed)
            if ($enfant->getEcole() === $this) {
                $enfant->setEcole(null);
            }
        }

        return $this;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addEcole($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeEcole($this);
        }

        return $this;
    }
}
