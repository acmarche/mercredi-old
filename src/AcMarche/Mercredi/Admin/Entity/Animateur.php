<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Plaine\Entity\AnimateurPlaine;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Animateur
 *
 * @ORM\Table("animateur")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\AnimateurRepository")
 *
 */
class Animateur implements \Serializable, UserPopulateInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Gedmo\Slug(fields={"nom", "prenom"}, separator="_")
     * @ORM\Column(length=62, unique=true)
     */
    protected $slugname;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     */
    protected $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=200, nullable=false)
     * @Assert\NotBlank()
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
     * @ORM\Column(name="email", type="string", length=200, nullable=false)
     *
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "tel"})
     */
    protected $telephone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "gsm"})
     */
    protected $gsm;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    protected $birthday;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true, options={"comment" = "sexe"})
     */
    protected $sexe;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $numero_national;

    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $num_assimilation;

    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $num_bancaire;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     *
     */
    protected $diplome;

    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $groupe_souhaite;

    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     */
    protected $taille_tshirt;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     *
     */
    protected $disponibilite;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $remarques;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = "0"})
     *
     */
    protected $archive = false;

    /**
     * @ORM\ManyToMany(targetEntity="Jour", inversedBy="animateurs", cascade={"persist"})
     *
     * */
    protected $jours;

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\AnimateurPlaine", mappedBy="animateur", cascade={"persist", "remove"})
     *
     */
    protected $plaines;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Security\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user_add;

    /**
     * @ORM\OneToOne(targetEntity="AcMarche\Mercredi\Security\Entity\User", inversedBy="animateur" )
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     *
     */
    protected $user;

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
     * PIECE JOINTE
     */

    /**
     *
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\File(
     *     maxSize = "7M",
     *     mimeTypes={ "application/pdf", "image/*" }
     * )
     * @var UploadedFile $file
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255, name="fileName", nullable=true)
     *
     * @var string $fileName
     */
    protected $fileName;

    /**
     *
     *
     * @param UploadedFile|\Symfony\Component\HttpFoundation\File\UploadedFile $file
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
     * @return UploadedFile
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
     */
    protected $imageName;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param UploadedFile|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setImage(UploadedFile $file = null)
    {
        $this->image = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return UploadedFile
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * certificat de bonne vie et moeurs
     */

    /**
     *
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\File(
     *     maxSize = "7M",
     *     mimeTypes={ "application/pdf", "image/*" }
     * )
     * @var UploadedFile $certificat
     */
    protected $certificat;

    /**
     * @ORM\Column(type="string", length=255, name="certificatName", nullable=true)
     *
     * @var string $certificatName
     */
    protected $certificatName;

    /**
     *
     *
     * @param UploadedFile|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setCertificat(File $file = null)
    {
        $this->certificat = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return UploadedFile
     */
    public function getCertificat()
    {
        return $this->certificat;
    }

    /**
     * diplome
     */

    /**
     *
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\File(
     *     maxSize = "7M",
     *     mimeTypes={ "application/pdf", "image/*" }
     * )
     * @var UploadedFile $diplome_file
     */
    protected $diplome_file;

    /**
     * @ORM\Column(type="string", length=255, name="diplomeName", nullable=true)
     *
     * @var string $diplomeName
     */
    protected $diplomeName;

    /**
     *
     *
     * @param UploadedFile|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setDiplomeFile(File $file = null)
    {
        $this->diplome_file = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return UploadedFile
     */
    public function getDiplomeFile()
    {
        return $this->diplome_file;
    }

    /**
     *
     * certificat de capacité de travail
     */

    /**
     *
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\File(
     *     maxSize = "7M",
     *     mimeTypes={ "application/pdf", "image/*" }
     * )
     * @var UploadedFile $casier
     */
    protected $casier;

    /**
     * @ORM\Column(type="string", length=255, name="casierName", nullable=true)
     *
     * @var string $casierName
     */
    protected $casierName;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->plaines = new ArrayCollection();
    }

    /**
     *
     *
     * @param UploadedFile|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setCasier(File $file = null)
    {
        $this->casier = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated = new \DateTime('now');
        }
    }

    /**
     * @return UploadedFile
     */
    public function getCasier()
    {
        return $this->casier;
    }

    public function __toString()
    {
        $txt = mb_strtoupper($this->getNom(), 'UTF-8')." ".$this->getPrenom();

        return $txt;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getNumeroNational(): ?string
    {
        return $this->numero_national;
    }

    public function setNumeroNational(?string $numero_national): self
    {
        $this->numero_national = $numero_national;

        return $this;
    }

    public function getNumAssimilation(): ?string
    {
        return $this->num_assimilation;
    }

    public function setNumAssimilation(?string $num_assimilation): self
    {
        $this->num_assimilation = $num_assimilation;

        return $this;
    }

    public function getNumBancaire(): ?string
    {
        return $this->num_bancaire;
    }

    public function setNumBancaire(?string $num_bancaire): self
    {
        $this->num_bancaire = $num_bancaire;

        return $this;
    }

    public function getDiplome(): ?string
    {
        return $this->diplome;
    }

    public function setDiplome(?string $diplome): self
    {
        $this->diplome = $diplome;

        return $this;
    }

    public function getGroupeSouhaite(): ?string
    {
        return $this->groupe_souhaite;
    }

    public function setGroupeSouhaite(?string $groupe_souhaite): self
    {
        $this->groupe_souhaite = $groupe_souhaite;

        return $this;
    }

    public function getTailleTshirt(): ?string
    {
        return $this->taille_tshirt;
    }

    public function setTailleTshirt(?string $taille_tshirt): self
    {
        $this->taille_tshirt = $taille_tshirt;

        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(?string $disponibilite): self
    {
        $this->disponibilite = $disponibilite;

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

    public function getCertificatName(): ?string
    {
        return $this->certificatName;
    }

    public function setCertificatName(?string $certificatName): self
    {
        $this->certificatName = $certificatName;

        return $this;
    }

    public function getDiplomeName(): ?string
    {
        return $this->diplomeName;
    }

    public function setDiplomeName(?string $diplomeName): self
    {
        $this->diplomeName = $diplomeName;

        return $this;
    }

    public function getCasierName(): ?string
    {
        return $this->casierName;
    }

    public function setCasierName(?string $casierName): self
    {
        $this->casierName = $casierName;

        return $this;
    }

    /**
     * @return Collection|Jour[]
     */
    public function getJours(): Collection
    {
        return $this->jours;
    }

    public function addJour(Jour $jour): self
    {
        if (!$this->jours->contains($jour)) {
            $this->jours[] = $jour;
        }

        return $this;
    }

    public function removeJour(Jour $jour): self
    {
        if ($this->jours->contains($jour)) {
            $this->jours->removeElement($jour);
        }

        return $this;
    }

    /**
     * @return Collection|AnimateurPlaine[]
     */
    public function getPlaines(): Collection
    {
        return $this->plaines;
    }

    public function addPlaine(AnimateurPlaine $plaine): self
    {
        if (!$this->plaines->contains($plaine)) {
            $this->plaines[] = $plaine;
            $plaine->setAnimateur($this);
        }

        return $this;
    }

    public function removePlaine(AnimateurPlaine $plaine): self
    {
        if ($this->plaines->contains($plaine)) {
            $this->plaines->removeElement($plaine);
            // set the owning side to null (unless already changed)
            if ($plaine->getAnimateur() === $this) {
                $plaine->setAnimateur(null);
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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
                $this->adresse,
                $this->imageName,
                $this->localite,

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

    public function getRoleByDefault(): string
    {
        return 'MERCREDI_ANIMATEUR';
    }
}
