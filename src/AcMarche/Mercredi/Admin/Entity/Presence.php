<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Security\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Presence.
 *
 * @ORM\Table("presence", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"jour_id", "enfant_id"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\PresenceRepository")
 * @UniqueEntity(fields={"jour", "enfant"}, message="L'enfant est déjà inscrit à cette date")
 */
class Presence
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
     * @ORM\ManyToOne(targetEntity="Jour", inversedBy="presences")
     * @ORM\JoinColumn(name="jour_id", referencedColumnName="id", nullable=false)
     *
     * @var Jour
     *
     * */
    protected $jour;

    /**
     * @ORM\ManyToOne(targetEntity="Enfant", inversedBy="presences")
     * @ORM\JoinColumn(name="enfant_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     *
     * */
    protected $enfant;

    /**
     * @ORM\ManyToOne(targetEntity="Tuteur", inversedBy="presences")
     * @ORM\JoinColumn(name="tuteur_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     *
     * */
    protected $tuteur;

    /**
     * @ORM\ManyToOne(targetEntity="Reduction", inversedBy="presence")
     */
    protected $reduction;

    /**
     * @ORM\ManyToOne(targetEntity="Paiement", inversedBy="presences", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $paiement;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=2, nullable=true, options={"comment" = "1,2, suviant", "default" = "0"})
     */
    protected $ordre = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=2, nullable=false, options={"comment" = "-1 sans certif, 1 avec certfi", "default" = "0"})
     */
    protected $absent = 0;

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
    //pour pouvoir ajouter plusieurs dates d'un coup
    protected $jours;
    //pour savoir si present le meme jour
    protected $fratries;

    /**
     * Tableau contenant le detail du cout de la presence
     * Voir Enfance::Calcul.
     *
     * @var array
     */
    protected $calcul;
    //prix plein suivant l'ordre
    protected $prix;
    //cout apres reduction
    protected $cout;

    protected $prixTmp;
    protected $coutTmp;

    protected $ordreNew;

    public function __toString()
    {
        $date_jour = $this->jour->getDateJour();

        if (is_a($date_jour, 'DateTime')) {
            $jour = $date_jour->format('D');
            switch ($jour) {
                case 'Mon':
                    $jourFr = 'Lundi';
                    break;
                case 'Tue':
                    $jourFr = 'Mardi';
                    break;
                case 'Wed':
                    $jourFr = 'Admin';
                    break;
                case 'Thu':
                    $jourFr = 'Jeudi';
                    break;
                case 'Fri':
                    $jourFr = 'Vendredi';
                    break;
                case 'Sat':
                    $jourFr = 'Samedi';
                    break;
                case 'Sun':
                    $jourFr = 'Dimanche';
                    break;
                default:
                    $jourFr = '';
                    break;
            }

            return $date_jour->format('d-m-Y').' '.$jourFr;
        } else {
            return '';
        }
    }

    public function getJours()
    {
        return $this->jours;
    }

    public function setJours(Jour $jour)
    {
        $this->jours = $jour;

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

    public function getCalcul()
    {
        return $this->calcul;
    }

    public function setCalcul(array $calcul)
    {
        $this->calcul = $calcul;
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

    /**
     * Pas besoin de paiement sur la presence.
     *
     * @return mixed
     */
    public function isGratuite()
    {
        if ($reduction = $this->getReduction()) {
            if (100 == $reduction->getPourcentage()) {
                return true;
            }
        }

        if (1 == $this->getAbsent()) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getPrixTmp()
    {
        return $this->prixTmp;
    }

    /**
     * @param mixed $prixTmp
     */
    public function setPrixTmp($prixTmp)
    {
        $this->prixTmp = $prixTmp;
    }

    /**
     * @return mixed
     */
    public function getCoutTmp()
    {
        return $this->coutTmp;
    }

    /**
     * @param mixed $coutTmp
     */
    public function setCoutTmp($coutTmp)
    {
        $this->coutTmp = $coutTmp;
    }

    /**
     * Utiliser dans form payerType.
     *
     * @return string
     */
    public function getAvecPrix()
    {
        $prix = '('.$this->getCout().' €)';

        return $this->__toString().' '.$prix;
    }

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->fratries = new ArrayCollection();
        $this->calcul = [];
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

    public function getJour(): ?Jour
    {
        return $this->jour;
    }

    public function setJour(?Jour $jour): self
    {
        $this->jour = $jour;

        return $this;
    }

    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }

    public function setEnfant(?Enfant $enfant): self
    {
        $this->enfant = $enfant;

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

    public function getReduction(): ?Reduction
    {
        return $this->reduction;
    }

    public function setReduction(?Reduction $reduction): self
    {
        $this->reduction = $reduction;

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
