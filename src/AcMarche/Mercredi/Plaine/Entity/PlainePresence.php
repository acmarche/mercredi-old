<?php

namespace AcMarche\Mercredi\Plaine\Entity;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Admin\Entity\Paiement;
use AcMarche\Mercredi\Admin\Entity\Tuteur;
use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Presence.
 *
 * @ORM\Table("plaine_presences", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"jour_id", "plaine_enfant_id"})
 * })))
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository")
 * @UniqueEntity(fields={"jour", "plaine_enfant"}, message="Email already taken")
 */
class PlainePresence
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PlaineJour", inversedBy="presences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $jour;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlaineEnfant", inversedBy="presences", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $plaine_enfant;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Tuteur", inversedBy="presences")
     * @ORM\JoinColumn(name="tuteur_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     *
     * */
    private $tuteur;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=2, nullable=true, options={"comment" = "1,2, suviant", "default" = "0"})
     */
    private $ordre = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=2, nullable=false, options={"comment" = "-1 sans certif, 1 avec certfi", "default" = "0"})
     */
    private $absent = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $remarques;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Paiement", inversedBy="plaine_presences")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $paiement;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Security\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user_add;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * Pour garder la ref de la plaine
     * lors de ajout enfant.
     */
    private $plaine;

    /**
     * Pour garder la ref de la plaine
     * lors de ajout enfant.
     */
    private $enfant;

    /**
     * Pour ajouter plusieurs jours d'un coup.
     */
    private $jours;

    /**
     * Pour attribuer un tuteur a des dates.
     */
    private $tuteurs;

    /**
     * pour savoir si present le meme jour.
     */
    private $fratries;

    /**
     * Prix de la journee suivant l'ordre.
     *
     * @var float
     */
    private $prix;

    /**
     * cout apres reduction.
     */
    private $cout;

    /**
     * Ordre en tenant compte de la fratrie.
     *
     * @var bool
     */
    private $ordreNew;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->fratries = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getJour()->getDateJour()->format('d-m-Y');
    }

    /**
     * @return Plaine
     */
    public function getPlaine()
    {
        return $this->plaine;
    }

    public function setPlaine(Plaine $plaine)
    {
        $this->plaine = $plaine;

        return $this;
    }

    public function setEnfant(Enfant $enfant)
    {
        $this->enfant = $enfant;

        return $this;
    }

    /**
     * @return Enfant
     */
    public function getEnfant()
    {
        return $this->enfant;
    }

    public function getTuteurs()
    {
        return $this->tuteurs;
    }

    public function addJour(PlaineJour $jour)
    {
        $this->jours[] = $jour;

        return $this;
    }

    public function addJours($jours)
    {
        foreach ($jours as $jour) {
            $this->addJour($jour);
        }

        return $this;
    }

    public function getJours()
    {
        return $this->jours;
    }

    public function setJours($jours)
    {
        return $this->jours = $jours;
    }

    public function addTuteur(Tuteur $tuteur)
    {
        $this->tuteurs[] = $tuteur;

        return $this;
    }

    public function addTuteurs($tuteurs)
    {
        foreach ($tuteurs as $tuteur) {
            $this->addTuteur($tuteur);
        }

        return $this;
    }

    public function getFratries()
    {
        return $this->fratries;
    }

    public function addFratrie(Enfant $enfant)
    {
        $this->fratries[] = $enfant;
    }

    public function addFratries(array $enfants)
    {
        $this->fratries = $enfants;
    }

    public function setPrix($prix)
    {
        $this->prix = $prix;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function setCout($cout)
    {
        $this->cout = $cout;
    }

    public function getCout()
    {
        return $this->cout;
    }

    public function setOrdreNew($ordre)
    {
        $this->ordreNew = $ordre;
    }

    public function getOrdreNew()
    {
        return $this->ordreNew;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAbsent(): ?int
    {
        return $this->absent;
    }

    public function setAbsent(int $absent): self
    {
        $this->absent = $absent;

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

    public function getJour(): ?PlaineJour
    {
        return $this->jour;
    }

    public function setJour(?PlaineJour $jour): self
    {
        $this->jour = $jour;

        return $this;
    }

    public function getPlaineEnfant(): ?PlaineEnfant
    {
        return $this->plaine_enfant;
    }

    public function setPlaineEnfant(?PlaineEnfant $plaine_enfant): self
    {
        $this->plaine_enfant = $plaine_enfant;

        return $this;
    }

    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function setTuteur(?Tuteur $tuteur): self
    {
        $this->tuteur = $tuteur;

        return $this;
    }

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): self
    {
        $this->paiement = $paiement;

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
