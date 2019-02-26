<?php

namespace AcMarche\Mercredi\Admin\Twig\Extension;

use AcMarche\Mercredi\Commun\Utils\DateService;

class DateJour extends \Twig_Extension
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
        return array(
            new \Twig_SimpleFilter('datefr', array($this, 'dateFilter')),
        );
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
