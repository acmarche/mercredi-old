<?php
/**
 * This file is part of mercredi application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 29/10/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AcMarche\Mercredi\Plaine\Service;

use AcMarche\Mercredi\Admin\Entity\Enfant;
use AcMarche\Mercredi\Commun\Utils\ScolaireService;
use AcMarche\Mercredi\Plaine\Entity\PlaineEnfant;
use Carbon\Carbon;

class GroupeScolaireService
{
    /**
     * @var ScolaireService
     */
    private $scolaireService;

    public function __construct(ScolaireService $scolaireService)
    {
        $this->scolaireService = $scolaireService;
    }

    /**
     * @param PlaineEnfant[] $plaineEnfants
     *
     * @return array|mixed
     */
    public function getEnfantsByGroupeScolaire(array $plaineEnfants, \DateTime $datePlaine)
    {
        $groupes = [];

        foreach ($plaineEnfants as $plaineEnfant) {
            $enfant = $plaineEnfant->getEnfant();
            $age = $this->getAge($enfant, $datePlaine);
            $groupe = $this->getGroupeByAge($age);
            $groupes[$groupe][] = $enfant;
        }

        return $groupes;
    }

    public function getGroupeByAge(int $age)
    {
        if ($age <= 4) {
            return 'petits';
        }

        if ($age <= 7) {
            return 'moyens';
        }

        return 'grands';
    }

    public function getAge(Enfant $enfant, \DateTime $datePlaine): int
    {
        $dateNaissance = $enfant->getBirthday();
        if (!$dateNaissance) {
            return 0;
        }
        $naissance = Carbon::instance($dateNaissance);

        return $naissance->diffInYears($datePlaine);
    }
}
