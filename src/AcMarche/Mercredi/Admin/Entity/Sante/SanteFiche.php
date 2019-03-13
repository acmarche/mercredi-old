<?php

namespace AcMarche\Mercredi\Admin\Entity\Sante;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use AcMarche\Mercredi\Parent\Validator\Constraints as AcMarcheAssert;

/**
 *
 * @ORM\Table("sante_fiche")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Admin\Repository\SanteFicheRepository")
 
 */
class SanteFiche
{
    use TimestampableEntity;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $personne_urgence;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $medecin_nom;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $medecin_telephone;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read", "write"})
     */
    protected $remarques;

    /**
     * @var Enfant $enfant
     * @ORM\OneToOne(targetEntity="AcMarche\Mercredi\Admin\Entity\Enfant", inversedBy="sante_fiche")
     * 
     */
    protected $enfant;

    /**
     * @var SanteReponse[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Admin\Entity\Sante\SanteReponse", mappedBy="sante_fiche", cascade={"remove"})
     *
     */
    protected $reponses;

    /**
     * @var SanteQuestion[]|ArrayCollection
     * @AcMarcheAssert\QuestionIsComplete()
     *
     */
    protected $questions;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function __toString()
    {
        return "Fiche ".$this->id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPersonneUrgence()
    {
        return $this->personne_urgence;
    }

    /**
     * @param mixed $personne_urgence
     */
    public function setPersonneUrgence($personne_urgence): void
    {
        $this->personne_urgence = $personne_urgence;
    }

    /**
     * @return null|string
     */
    public function getMedecinNom(): ?string
    {
        return $this->medecin_nom;
    }

    /**
     * @param null|string $medecin_nom
     */
    public function setMedecinNom(?string $medecin_nom): void
    {
        $this->medecin_nom = $medecin_nom;
    }

    /**
     * @return null|string
     */
    public function getMedecinTelephone(): ?string
    {
        return $this->medecin_telephone;
    }

    /**
     * @param null|string $medecin_telephone
     */
    public function setMedecinTelephone(?string $medecin_telephone): void
    {
        $this->medecin_telephone = $medecin_telephone;
    }

    /**
     * @return null|string
     */
    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    /**
     * @param null|string $remarques
     */
    public function setRemarques(?string $remarques): void
    {
        $this->remarques = $remarques;
    }

    /**
     * @return Enfant
     */
    public function getEnfant(): Enfant
    {
        return $this->enfant;
    }

    /**
     * @param Enfant $enfant
     */
    public function setEnfant(Enfant $enfant): void
    {
        $this->enfant = $enfant;
    }

    /**
     * @return SanteReponse[]
     */
    public function getReponses(): array
    {
        return $this->reponses;
    }

    /**
     * @param SanteReponse[] $reponses
     */
    public function setReponses(array $reponses): void
    {
        $this->reponses = $reponses;
    }

    /**
     * @return SanteQuestion[]|ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param SanteQuestion[]|ArrayCollection $questions
     */
    public function setQuestions($questions): void
    {
        $this->questions = $questions;
    }

    public function addSanteQuestion(SanteQuestion $santeQuestion): self
    {
        if (!$this->questions->contains($santeQuestion)) {
            $this->questions[] = $santeQuestion;
            $santeQuestion->setSanteFiche($this);
        }

        return $this;
    }

    public function removeSanteQuestion(SanteQuestion $santeQuestion): self
    {
        if ($this->questions->contains($santeQuestion)) {
            $this->questions->removeElement($santeQuestion);
            // set the owning side to null (unless already changed)
            // if ($santeQuestion->getEnfant() === $this) {
            //   $santeQuestion->setEnfant(null);
            // }
        }

        return $this;
    }

    public function addReponse(SanteReponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setSanteFiche($this);
        }

        return $this;
    }

    public function removeReponse(SanteReponse $reponse): self
    {
        if ($this->reponses->contains($reponse)) {
            $this->reponses->removeElement($reponse);
            // set the owning side to null (unless already changed)
            if ($reponse->getSanteFiche() === $this) {
                $reponse->setSanteFiche(null);
            }
        }

        return $this;
    }

}
