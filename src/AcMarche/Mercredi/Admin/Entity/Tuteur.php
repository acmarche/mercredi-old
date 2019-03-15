<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Parent\Validator\Constraints as AcMarcheAssert;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tuteur
 *
 * @ORM\Table("tuteur",indexes={@Orm\Index(name="search_idx", columns={"nom", "email"})})
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\TuteurRepository")
 * @AcMarcheAssert\TelephoneIsComplete()
 *
 */
class Tuteur implements UserPopulateInterface
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
     * @Gedmo\Slug(fields={"nom", "prenom"}, separator="_")
     * @ORM\Column(length=62, unique=true)
     *
     */
    protected $slugname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $civilite;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $prenom;

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
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "tel"})
     *
     */
    protected $telephone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "tel bureau"})
     *
     */
    protected $telephone_bureau;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "gsm"})
     *
     */
    protected $gsm;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=200, nullable=true)
     *
     *
     */
    protected $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     *
     */
    protected $birthday;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "sexe"})
     *
     */
    protected $sexe;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true, options={"comment" = "belle-mere, pere, mere"})
     *
     *
     */
    protected $conjoint;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $nom_conjoint;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $prenom_conjoint;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "tel"})
     *
     */
    protected $telephone_conjoint;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "tel bureau"})
     *
     */
    protected $telephone_bureau_conjoint;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "gsm"})
     *
     */
    protected $gsm_conjoint;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "email"})
     *
     */
    protected $email_conjoint;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" = 0})
     *
     */
    protected $composition_menage = false;

    /**
     * @var integer
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    protected $archive = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
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

    /**
     * @ORM\ManyToOne(targetEntity="CaisseAllocation", inversedBy="tuteur")
     *
     */
    protected $caisse_allocation;

    /**
     * @ORM\OneToMany(targetEntity="EnfantTuteur", mappedBy="tuteur", cascade={"persist", "remove"})
     *
     * */
    protected $enfants;

    /**
     * Utilise lorsqu'on ajoute un tuteur avec la ref d'un enfant
     * @var Enfant
     */
    protected $enfant;

    /**
     * @ORM\OneToMany(targetEntity="Paiement", mappedBy="tuteur", cascade={"remove"})
     * @ORM\OrderBy({"date_paiement" = "DESC"})
     */
    protected $paiements;

    /**
     * @ORM\OneToMany(targetEntity="Presence", mappedBy="tuteur", cascade={"remove"})
     * @ORM\JoinColumn(name="tuteur_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     * */
    protected $presences;

    /**
     * @ORM\OneToOne(targetEntity="AcMarche\Mercredi\Security\Entity\User", inversedBy="tuteur" )
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     *
     */
    protected $user;

    public function __construct()
    {
        $this->enfants = new ArrayCollection();
        $this->paiements = new ArrayCollection();
        $this->presences = new ArrayCollection();
    }

    public function __toString()
    {
        return mb_strtoupper($this->getNom(), 'UTF-8')." ".$this->getPrenom();
    }

    public function getAdresseComplete()
    {
        return $this->getAdresse().'<br />'.$this->code_postal.' '.$this->localite;
    }

    /**
     * @return Enfant
     */
    public function getEnfant()
    {
        return $this->enfant;
    }

    /**
     * @param Enfant $enfant
     * @return Tuteur
     */
    public function setEnfant($enfant)
    {
        $this->enfant = $enfant;

        return $this;
    }

    public function getPaimentsByYear($year)
    {
        $paiements = array();

        foreach ($this->getPaiements() as $paiement) {
            $datePaiement = $paiement->getDatePaiement();
            if ($datePaiement->format('Y') == $year) {
                $paiements[] = $paiement;
            }
        }

        return $paiements;
    }

    public function getPaimentsNonCloture()
    {
        $paiements = array();

        foreach ($this->getPaiements() as $paiement) {
            if (!$paiement->getCloture()) {
                $paiements[] = $paiement;
            }
        }

        return $paiements;
    }

    /**
     * pour api
     * @param $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): UserPopulateInterface
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): UserPopulateInterface
    {
        $this->prenom = $prenom;

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

    public function getTelephoneBureau(): ?string
    {
        return $this->telephone_bureau;
    }

    public function setTelephoneBureau(?string $telephone_bureau): self
    {
        $this->telephone_bureau = $telephone_bureau;

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

    public function setEmail(?string $email): UserPopulateInterface
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getConjoint(): ?string
    {
        return $this->conjoint;
    }

    public function setConjoint(?string $conjoint): self
    {
        $this->conjoint = $conjoint;

        return $this;
    }

    public function getNomConjoint(): ?string
    {
        return $this->nom_conjoint;
    }

    public function setNomConjoint(?string $nom_conjoint): self
    {
        $this->nom_conjoint = $nom_conjoint;

        return $this;
    }

    public function getPrenomConjoint(): ?string
    {
        return $this->prenom_conjoint;
    }

    public function setPrenomConjoint(?string $prenom_conjoint): self
    {
        $this->prenom_conjoint = $prenom_conjoint;

        return $this;
    }

    public function getTelephoneConjoint(): ?string
    {
        return $this->telephone_conjoint;
    }

    public function setTelephoneConjoint(?string $telephone_conjoint): self
    {
        $this->telephone_conjoint = $telephone_conjoint;

        return $this;
    }

    public function getTelephoneBureauConjoint(): ?string
    {
        return $this->telephone_bureau_conjoint;
    }

    public function setTelephoneBureauConjoint(?string $telephone_bureau_conjoint): self
    {
        $this->telephone_bureau_conjoint = $telephone_bureau_conjoint;

        return $this;
    }

    public function getGsmConjoint(): ?string
    {
        return $this->gsm_conjoint;
    }

    public function setGsmConjoint(?string $gsm_conjoint): self
    {
        $this->gsm_conjoint = $gsm_conjoint;

        return $this;
    }

    public function getEmailConjoint(): ?string
    {
        return $this->email_conjoint;
    }

    public function setEmailConjoint(?string $email_conjoint): self
    {
        $this->email_conjoint = $email_conjoint;

        return $this;
    }

    public function getCompositionMenage(): ?bool
    {
        return $this->composition_menage;
    }

    public function setCompositionMenage(?bool $composition_menage): self
    {
        $this->composition_menage = $composition_menage;

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

    public function getUserAdd(): ?User
    {
        return $this->user_add;
    }

    public function setUserAdd(?User $user_add): self
    {
        $this->user_add = $user_add;

        return $this;
    }

    public function getCaisseAllocation(): ?CaisseAllocation
    {
        return $this->caisse_allocation;
    }

    public function setCaisseAllocation(?CaisseAllocation $caisse_allocation): self
    {
        $this->caisse_allocation = $caisse_allocation;

        return $this;
    }

    /**
     * @return Collection|EnfantTuteur[]
     */
    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function addEnfant(EnfantTuteur $enfant): self
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->setTuteur($this);
        }

        return $this;
    }

    public function removeEnfant(EnfantTuteur $enfant): self
    {
        if ($this->enfants->contains($enfant)) {
            $this->enfants->removeElement($enfant);
            // set the owning side to null (unless already changed)
            if ($enfant->getTuteur() === $this) {
                $enfant->setTuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Paiement[]
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements[] = $paiement;
            $paiement->setTuteur($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->contains($paiement)) {
            $this->paiements->removeElement($paiement);
            // set the owning side to null (unless already changed)
            if ($paiement->getTuteur() === $this) {
                $paiement->setTuteur(null);
            }
        }

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
            $presence->setTuteur($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getTuteur() === $this) {
                $presence->setTuteur(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRoleByDefault(): string
    {
        return 'MERCREDI_PARENT';
    }
}
