<?php

namespace AcMarche\Mercredi\Plaine\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("plaine_max", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"plaine_id", "groupe"})
 * }))
 * @UniqueEntity({"plaine", "groupe"})
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineMaxRepository")
 */
class PlaineMax
{
    /**
     * @var integer|null $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null $groupe
     *
     * @ORM\Column(type="string", length=50)
     */
    private $groupe;

    /**
     * @var Plaine|null $plaine
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Plaine\Entity\Plaine", inversedBy="max", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $plaine;

    /**
     * @var integer|null
     * @ORM\Column(type="integer")
     */
    private $maximum;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getGroupe(): ?string
    {
        return $this->groupe;
    }

    /**
     * @param null|string $groupe
     */
    public function setGroupe(?string $groupe): void
    {
        $this->groupe = $groupe;
    }

    /**
     * @return Plaine|null
     */
    public function getPlaine(): ?Plaine
    {
        return $this->plaine;
    }

    /**
     * @param Plaine|null $plaine
     */
    public function setPlaine(?Plaine $plaine): void
    {
        $this->plaine = $plaine;
    }

    /**
     * @return int|null
     */
    public function getMaximum(): ?int
    {
        return $this->maximum;
    }

    /**
     * @param int|null $maximum
     */
    public function setMaximum(?int $maximum): void
    {
        $this->maximum = $maximum;
    }
}
