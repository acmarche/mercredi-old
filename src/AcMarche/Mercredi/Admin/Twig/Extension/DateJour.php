<?php

namespace AcMarche\Mercredi\Admin\Twig\Extension;

use AcMarche\Mercredi\Commun\Utils\DateService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateJour extends AbstractExtension
{
    /**
     * @var DateService
     */
    private $dateService;

    public function __construct(DateService $dateService)
    {
        $this->dateService = $dateService;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('datefr', [$this, 'dateFilter']),
        ];
    }

    public function dateFilter(\DateTime $date, $jourFirst = false)
    {
        return $this->dateService->getFr($date, $jourFirst);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date_jour';
    }
}
