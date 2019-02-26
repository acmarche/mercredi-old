<?php

namespace AcMarche\Mercredi\Admin\Entity;

use AcMarche\Mercredi\Plaine\Entity\PlainePresence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Relation en le parent et son enfant
 *
 * @ORM\Table("enfant_tuteur", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"tuteur_id", "enfant_id"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository")
 * @UniqueEntity(fields={"tuteur", "enfant"}, message="Cette enfant est déjà lié à ce parent")
 *
 */
class EnfantTuteur
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
     * @var integer
     *
     * @ORM\Column(type="smallint", length=2, nullable=true, options={"comment" = "1,2, suviant", "default" = "0"})
     *
     */
    protected $ordre = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true, options={"comment" = "pere,mere,beau pere.."})
     *
     */
    protected $relation;

    /**
     * @ORM\ManyToOne(targetEntity="Enfant", inversedBy="tuteurs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     * */
    protected $enfant;

    /**
     * @var Tuteur $tuteur
     * @ORM\ManyToOne(targetEntity="Tuteur", inversedBy="enfants", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     * */
    protected $tuteur;

    /**
     * @var Presence[]|ArrayCollection $presences
     */
    protected $presences;
    /**
     * @var Presence[]|ArrayCollection $presences_non_payes
     */
    protected $presences_non_payes;
    /**
     * @var PlainePresence[]|ArrayCollection $plaine_presences_non_payes
     */
    protected $plaine_presences_non_payes;
    /**
     * @var Paiement[]|ArrayCollection $presences
     */
    protected $paiements;
    protected $presencesByMonth;

    public function __construct()
    {
        $this->presences = new ArrayCollection();
        $this->presences_non_payes = new ArrayCollection();
        $this->plaine_presences_non_payes = new ArrayCollection();
        $this->paiements = new ArrayCollection();
    }

    public function __toString()
    {
        return '';
    }

    public function __get($prop)
    {
        return $this->$prop;
    }

    public function __isset($prop): bool
    {
        return isset($this->$prop);
    }

    public function getPresences()
    {
        return $this->presences;
    }

    public function addPresences($presences)
    {
        foreach ($presences as $presence) {
            $this->addPresence($presence);
        }

        return $this;
    }

    /**
     * @param Presence $presence
     * @return $this
     */
    public function addPresence(Presence $presence)
    {
        $this->presences[$presence->getId()] = $presence;

        return $this;
    }

    public function removePresences()
    {
        $this->presences = new ArrayCollection();

        return $this;
    }

    public function getPresencesNonPayes()
    {
        return $this->presences_non_payes;
    }

    public function addPresencesNonPayes($presences)
    {
        foreach ($presences as $presence) {
            $this->addPresenceNonPaye($presence);
        }

        return $this;
    }

    public function addPresenceNonPaye(Presence $presence)
    {
        $this->presences_non_payes[$presence->getId()] = $presence;

        return $this;
    }

    public function getPlainePresencesNonPayes()
    {
        return $this->plaine_presences_non_payes;
    }

    public function addPlainePresencesNonPayes($presences)
    {
        foreach ($presences as $presence) {
            $this->addPlainePresenceNonPaye($presence);
        }

        return $this;
    }

    public function addPlainePresenceNonPaye(PlainePresence $presence)
    {
        $this->plaine_presences_non_payes[$presence->getId()] = $presence;

        return $this;
    }

    public function getPaiements()
    {
        return $this->paiements;
    }

    public function addPaiements($paiements)
    {
        foreach ($paiements as $paiement) {
            $this->addPaiement($paiement);
        }

        return $this;
    }

    /**
     * @param Paiement $paiement
     * @return $this
     */
    public function addPaiement(Paiement $paiement)
    {
        $this->paiements[$paiement->getId()] = $paiement;

        return $this;
    }

    public function addPresencesByMonth(array $presences)
    {
        $this->presencesByMonth = $presences;

        return $this;
    }

    public function getPresencesByMonth($year = null)
    {
        if ($year) {
            $presences = isset($this->presencesByMonth[$year]) ? $this->presencesByMonth[$year] : array();

            return $presences;
        }

        return $this->presencesByMonth;
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

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function setRelation(?string $relation): self
    {
        $this->relation = $relation;

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

    /**
     * STOP
     */
}
