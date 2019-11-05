<?php

namespace AcMarche\Mercredi\Security\Entity;

use AcMarche\Mercredi\Admin\Entity\Animateur;
use AcMarche\Mercredi\Admin\Entity\Ecole;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Security\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $enabled;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     *
     * @var string|null
     */
    protected $token;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(
     *      min = 6
     *     )
     *
     * @var string
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @Assert\Length(
     *      min = 6
     *     )
     *
     * @var string|null
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime|null
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @ORM\Column(name="confirmation_token",type="string", length=180, unique=true, nullable=true)
     *
     * @var string|null
     */
    protected $confirmationToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="password_requested_at",type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @ORM\Column(type="array")
     *
     * @var array
     */
    protected $roles;

    /**
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Security\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="fos_user_group")
     */
    protected $groups;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @Assert\Length(
     *     min=3
     * )
     *
     * @var string|null
     */
    private $nom;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Length(
     *     min=3
     * )
     *
     * @var string|null
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $adresse;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", length=6, nullable=true)
     */
    protected $code_postal;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $localite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "tel"})
     */
    protected $telephone;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accord;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $accord_date;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $api_token;

    /**
     * @ORM\OneToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Tuteur", mappedBy="user", cascade={"persist"})
     *
     * @var Tuteur|null
     */
    protected $tuteur;

    /**
     * @ORM\OneToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Animateur", mappedBy="user", cascade={"persist"})
     *
     * @var Animateur|null
     */
    protected $animateur;

    /**
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Admin\Entity\Ecole", inversedBy="users")
     */
    protected $ecoles;

    /**
     * Parent ou ecole.
     *
     * @var string|null
     */
    private $type;

    /**
     * Pour rappel mot de passe, probleme email deja utilise.
     *
     * @Assert\Email()
     *
     * @var string|null
     */
    private $email_request;

    public function __construct()
    {
        $this->enabled = true;
        $this->roles = [];
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->accord_date = new \DateTime();
        $this->ecoles = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) mb_strtoupper($this->nom, 'UTF-8').' '.$this->prenom;
    }

    public function isParent()
    {
        if (in_array('ROLE_MERCREDI_PARENT', $this->getRoles())) {
            return true;
        }

        return false;
    }

    public function isAnimateur()
    {
        if (in_array('ROLE_MERCREDI_ANIMATEUR', $this->getRoles())) {
            return true;
        }

        return false;
    }

    public function isEcole()
    {
        if (in_array('ROLE_MERCREDI_ECOLE', $this->getRoles())) {
            return true;
        }

        return false;
    }

    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function getAdresseComplete()
    {
        return $this->adresse.'<br />'.$this->code_postal.' '.$this->localite;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            foreach ($group->getRoles() as $role) {
                $roles[] = $role;
            }
        }

        // we need to make sure to have at least one role
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getEmailRequest(): ?string
    {
        return $this->email_request;
    }

    public function setEmailRequest(?string $email_request): void
    {
        $this->email_request = $email_request;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * Ajout de if ($tuteur !== null) {.
     *
     * @return User
     */
    public function setTuteur(?Tuteur $tuteur): self
    {
        $this->tuteur = $tuteur;
        if (null !== $tuteur) {
            // set (or unset) the owning side of the relation if necessary
            $newUser = null === $tuteur ? null : $this;
            if ($newUser !== $tuteur->getUser()) {
                $tuteur->setUser($newUser);
            }
        }

        return $this;
    }

    public function setAnimateur(?Animateur $animateur): self
    {
        $this->animateur = $animateur;
        if (null != $animateur) {
            // set (or unset) the owning side of the relation if necessary
            $newUser = null === $animateur ? null : $this;
            if ($newUser !== $animateur->getUser()) {
                $animateur->setUser($newUser);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt): self
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAccord(): ?bool
    {
        return $this->accord;
    }

    public function setAccord(?bool $accord): self
    {
        $this->accord = $accord;

        return $this;
    }

    public function getAccordDate(): ?\DateTimeInterface
    {
        return $this->accord_date;
    }

    public function setAccordDate(?\DateTimeInterface $accord_date): self
    {
        $this->accord_date = $accord_date;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->api_token;
    }

    public function setApiToken(?string $api_token): self
    {
        $this->api_token = $api_token;

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }

    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function getAnimateur(): ?Animateur
    {
        return $this->animateur;
    }

    /**
     * @return Collection|Ecole[]
     */
    public function getEcoles(): Collection
    {
        return $this->ecoles;
    }

    public function addEcole(Ecole $ecole): self
    {
        if (!$this->ecoles->contains($ecole)) {
            $this->ecoles[] = $ecole;
        }

        return $this;
    }

    public function removeEcole(Ecole $ecole): self
    {
        if ($this->ecoles->contains($ecole)) {
            $this->ecoles->removeElement($ecole);
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

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
}
