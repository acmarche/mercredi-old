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

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Admin\Entity\Accompagnateur", mappedBy="ecole", orphanRemoval=true)
     */
    private $accompagnateurs;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->enfants = new ArrayCollection();
        $this->accompagnateurs = new ArrayCollection();
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->code_postal;
    }

    public function setCodePostal(?int $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function setLocalite(?string $localite): self
    {
        $this->localite = $localite;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getGsm(): ?string
    {
        return $this->gsm;
    }

    public function setGsm(?string $gsm): self
    {
        $this->gsm = $gsm;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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
     * @return Collection|Accompagnateur[]
     */
    public function getAccompagnateurs(): Collection
    {
        return $this->accompagnateurs;
    }

    public function addAccompagnateur(Accompagnateur $accompagnateur): self
    {
        if (!$this->accompagnateurs->contains($accompagnateur)) {
            $this->accompagnateurs[] = $accompagnateur;
            $accompagnateur->setEcole($this);
        }

        return $this;
    }

    public function removeAccompagnateur(Accompagnateur $accompagnateur): self
    {
        if ($this->accompagnateurs->contains($accompagnateur)) {
            $this->accompagnateurs->removeElement($accompagnateur);
            // set the owning side to null (unless already changed)
            if ($accompagnateur->getEcole() === $this) {
                $accompagnateur->setEcole(null);
            }
        }

        return $this;
    }
}
