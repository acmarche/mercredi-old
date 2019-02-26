<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Admin\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

// gedmo annotations

/**
 * Enfant
 *
 * @ORM\Table("enfant")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\EnfantRepository")
 *
 */
class Enfant implements \Serializable
{
    use UuidTrait;

    /**
     * Oblige override sinon bug
     * https://github.com/doctrine/doctrine2/issues/7215
     * @var Uuid
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * 
     */
    private $uuid;

    /**
     * @var integer
     *
     * IMPORTANT! This field annotation must be the last one in order to prevent
     * that Doctrine will use UuidGenerator as $`class->idGenerator`!
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
     * @ORM\Column(name="nom", type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     * 
     */
    protected $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $prenom;

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
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $numero_national;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "sexe"})
     *
     */
    protected $sexe;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", length=2, nullable=true, options={"comment" = "1,2, suviant", "default" = "0"})
     * @Assert\NotBlank()
     *
     */
    protected $ordre = 0;

    /**
     * @var Ecole
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Ecole", inversedBy="enfants")
     *
     */
    protected $ecole;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $annee_scolaire;

    /**
     * @var string
     * Forcer le groupe scolaire
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $groupe_scolaire;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     *
     */
    protected $remarques;

    /**
     * @var EnfantTuteur[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Admin\Entity\EnfantTuteur", mappedBy="enfant", cascade={"persist", "remove"})
     *
     * */
    protected $tuteurs;

    /**
     * @var Presence[]|null
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Admin\Entity\Presence", mappedBy="enfant", cascade={"remove"})
     *
     */
    protected $presences;

    /**
     * @var PlaineEnfant[]|null
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlaineEnfant", mappedBy="enfant", cascade={"remove"})
     *
     */
    protected $plaines;

    /**
     * @var Paiement[]|null
     * @ORM\OneToMany(targetEntity="Paiement", mappedBy="enfant")
     *
     */
    protected $paiements;

    /**
     * @var Note[]|null
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Admin\Entity\Note", mappedBy="enfant", cascade={"remove"})
     *
     */
    protected $notes;

    /**
     * @var SanteFiche $sante_fiche |null
     * @ORM\OneToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Sante\SanteFiche", mappedBy="enfant" )
     *
     */
    protected $sante_fiche;

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
    protected $created;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated;

    /**
     * Utilise lorsqu'on ajoute un enfant avec la ref d'un tuteur
     * @var Tuteur
     */
    protected $tuteur;

    /**
     * Pour autocompletion
     */
    protected $nom_birthday;

    /**
     * Pour listing
     * @var string
     */
    protected $telephones;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    protected $archive = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    protected $photo_autorisation = false;

    /**
     * @var string[]
     *
     * @ORM\Column(type="simple_array", nullable=true)
     *
     */
    protected $accompagnateurs = [];

    /**
     * pour changement niveau scolaire 1x/an
     */
    protected $new_scolaire;

    /**
     * @var boolean $sante_fiche_complete
     * 
     */
    protected $sante_fiche_complete = false;

    /**
     * @var boolean $fiche_complete
     */
    protected $fiche_complete = false;

    public function setNewScolaire($scolaire)
    {
        $this->new_scolaire = $scolaire;

        return $this;
    }

    public function getNewScolaire()
    {
        return $this->new_scolaire;
    }

    /**
     * pour affichage presence fratrie
     * @var boolean
     */
    protected $absent;

    public function setAbsent($absent)
    {
        $this->absent = $absent;

        return $this;
    }

    public function getAbsent()
    {
        return $this->absent;
    }

    /**
     * PIECE JOINTE
     */

    /**
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\File(
     *     maxSize = "7M",
     *     mimeTypes={ "application/pdf", "image/*" }
     * )
     * @var UploadedFile $file
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255, name="file_name", nullable=true)
     *
     * @var string $fileName
     *
     */
    protected $fileName;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * PHOTO
     */

    /**
     *
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\Image(
     *     maxSize = "5M"
     * )
     * @var UploadedFile $image
     */
    protected $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string $imageName
     *
     */
    protected $imageName;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setImage(File $file = null)
    {
        $this->image = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Fiche inscription
     */

    /**
     *
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\File(
     *     maxSize = "7M",
     *     mimeTypes={ "application/pdf", "image/*" }
     * )
     * @var UploadedFile $fiche
     */
    protected $fiche;

    /**
     * @ORM\Column(type="string", length=255, name="fiche_name", nullable=true)
     *
     * @var string $ficheName
     */
    protected $ficheName;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $fiche
     */
    public function setFiche(File $fiche = null)
    {
        $this->fiche = $fiche;

        if ($fiche) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getFiche()
    {
        return $this->fiche;
    }

    public function __toString()
    {
        $txt = mb_strtoupper($this->getNom(), 'UTF-8')." ".$this->getPrenom();

        return $txt;
    }

    /*
     * pour affichage auto completion
     */

    public function getNomBirthday()
    {
        return $this->getNom()." ".$this->getPrenom()." ".$this->getBirthday()->format("m-d-Y");
    }

    /**
     * quand ajoute tuteur
     */
    public function getTuteur()
    {
        return $this->tuteur;
    }

    /**
     * @param $tuteur
     * @return $this
     */
    public function setTuteur($tuteur)
    {
        $this->tuteur = $tuteur;

        return $this;
    }

    public function getAge(\DateTime $date_reference = null, $month = false)
    {
        $birthday = $this->getBirthday();

        if (!$birthday) {
            return 0;
        } else {
            if ($date_reference) {
                $today = $date_reference;
            } else {
                $today = new \DateTime();
            }
            $date = $birthday->diff($today);
            if ($month) {
                return $date->format('%y ans et %m mois');
            }

            return $date->format('%y');
        }
    }

    public function setTelephones($telephones)
    {
        $this->telephones = $telephones;

        return $this;
    }

    public function getTelephones()
    {
        return $this->telephones;
    }

    public function addAccompagnateur($accompagnateur)
    {
        $this->accompagnateurs[] = $accompagnateur;

        return $this;
    }

    public function removeAccompagnateur($accompagnateur)
    {
        $key = array_search($accompagnateur, $this->accompagnateurs);
        if (isset($this->accompagnateurs[$key])) {
            unset($this->accompagnateurs[$key]);
        }

        return $this;
    }

    /**
     * Set accompagnateurs
     *
     * @param array $accompagnateurs
     *
     * @return Enfant
     */
    public function setAccompagnateurs($accompagnateurs)
    {
        $this->accompagnateurs = $accompagnateurs;

        return $this;
    }

    /**
     * Utilise pour inscription plaine par un parent
     * @var
     */
    protected $inscrit = false;

    /**
     * Utilise pour inscription plaine par un parent
     * @var
     */
    protected $presencesPlaine;

    /**
     * Utilise pour inscription plaine par un parent
     * @var
     */
    protected $totalPlaine;

    public function __construct()
    {
        $this->tuteurs = new ArrayCollection();
        $this->presences = new ArrayCollection();
        $this->plaines = new ArrayCollection();
        $this->paiements = new ArrayCollection();
        $this->notes = new ArrayCollection();
        try {
            $this->uuid = \Ramsey\Uuid\Uuid::uuid4();
        } catch (\Exception $e) {
        }
    }

    /**
     * @return mixed
     */
    public function getInscrit()
    {
        return $this->inscrit;
    }

    /**
     * @param mixed $inscrit
     */
    public function setInscrit($inscrit)
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return mixed
     */
    public function getPresencesPlaine()
    {
        return $this->presencesPlaine;
    }

    /**
     * @param mixed $presencesPlaine
     */
    public function setPresencesPlaine($presencesPlaine)
    {
        $this->presencesPlaine = $presencesPlaine;
    }

    /**
     * @return mixed
     */
    public function getTotalPlaine()
    {
        return $this->totalPlaine;
    }

    /**
     * @param mixed $totalPlaine
     */
    public function setTotalPlaine($totalPlaine)
    {
        $this->totalPlaine = $totalPlaine;
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

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getNumeroNational(): ?string
    {
        return $this->numero_national;
    }

    public function setNumeroNational(?string $numero_national): self
    {
        $this->numero_national = $numero_national;

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

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getEcoleOld(): ?string
    {
        return $this->ecole_old;
    }

    public function setEcoleOld(?string $ecole_old): self
    {
        $this->ecole_old = $ecole_old;

        return $this;
    }

    public function getAnneeScolaire(): ?string
    {
        return $this->annee_scolaire;
    }

    public function setAnneeScolaire(string $annee_scolaire): self
    {
        $this->annee_scolaire = $annee_scolaire;

        return $this;
    }

    public function getGroupeScolaire(): ?string
    {
        return $this->groupe_scolaire;
    }

    public function setGroupeScolaire(?string $groupe_scolaire): self
    {
        $this->groupe_scolaire = $groupe_scolaire;

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

    public function getArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(bool $archive): self
    {
        $this->archive = $archive;

        return $this;
    }

    public function getPhotoAutorisation(): ?bool
    {
        return $this->photo_autorisation;
    }

    public function setPhotoAutorisation(bool $photo_autorisation): self
    {
        $this->photo_autorisation = $photo_autorisation;

        return $this;
    }

    public function getAccompagnateurs(): ?array
    {
        return $this->accompagnateurs;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getFicheName(): ?string
    {
        return $this->ficheName;
    }

    public function setFicheName(?string $ficheName): self
    {
        $this->ficheName = $ficheName;

        return $this;
    }

    public function getEcole(): ?Ecole
    {
        return $this->ecole;
    }

    public function setEcole(?Ecole $ecole): self
    {
        $this->ecole = $ecole;

        return $this;
    }

    /**
     * @return Collection|EnfantTuteur[]
     */
    public function getTuteurs(): Collection
    {
        return $this->tuteurs;
    }

    public function addTuteur(EnfantTuteur $tuteur): self
    {
        if (!$this->tuteurs->contains($tuteur)) {
            $this->tuteurs[] = $tuteur;
            $tuteur->setEnfant($this);
        }

        return $this;
    }

    public function removeTuteur(EnfantTuteur $tuteur): self
    {
        if ($this->tuteurs->contains($tuteur)) {
            $this->tuteurs->removeElement($tuteur);
            // set the owning side to null (unless already changed)
            if ($tuteur->getEnfant() === $this) {
                $tuteur->setEnfant(null);
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
            $presence->setEnfant($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getEnfant() === $this) {
                $presence->setEnfant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlaineEnfant[]
     */
    public function getPlaines(): Collection
    {
        return $this->plaines;
    }

    public function addPlaine(PlaineEnfant $plaine): self
    {
        if (!$this->plaines->contains($plaine)) {
            $this->plaines[] = $plaine;
            $plaine->setEnfant($this);
        }

        return $this;
    }

    public function removePlaine(PlaineEnfant $plaine): self
    {
        if ($this->plaines->contains($plaine)) {
            $this->plaines->removeElement($plaine);
            // set the owning side to null (unless already changed)
            if ($plaine->getEnfant() === $this) {
                $plaine->setEnfant(null);
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
            $paiement->setEnfant($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->contains($paiement)) {
            $this->paiements->removeElement($paiement);
            // set the owning side to null (unless already changed)
            if ($paiement->getEnfant() === $this) {
                $paiement->setEnfant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setEnfant($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getEnfant() === $this) {
                $note->setEnfant(null);
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
     * @return SanteFiche|null
     */
    public function getSanteFiche(): ?SanteFiche
    {
        return $this->sante_fiche;
    }

    /**
     * @param SanteFiche $sante_fiche
     */
    public function setSanteFiche(SanteFiche $sante_fiche): void
    {
        $this->sante_fiche = $sante_fiche;
    }

    /**
     * @return bool
     */
    public function isSanteFicheComplete(): bool
    {
        return $this->sante_fiche_complete;
    }

    /**
     * @param bool $sante_fiche_complete
     */
    public function setSanteFicheComplete(bool $sante_fiche_complete): void
    {
        $this->sante_fiche_complete = $sante_fiche_complete;
    }

    /**
     * @return bool
     */
    public function isFicheComplete(): bool
    {
        return $this->fiche_complete;
    }

    /**
     * @param bool $fiche_complete
     */
    public function setFicheComplete(bool $fiche_complete): void
    {
        $this->fiche_complete = $fiche_complete;
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->ecole,
                $this->imageName,
                $this->numero_national,

            )
        );
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,

            ) = unserialize($serialized);
    }
}
