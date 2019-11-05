<?php

namespace AcMarche\Mercredi\Admin\Twig;

use AcMarche\Mercredi\Admin\Service\EnfanceData;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MercrediExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('monthtext', [$this, 'monthFilter']),
            new TwigFilter('absencetext', [$this, 'absenceFilter']),
        ];
    }

    public function getFunctions()
    {
        return [new TwigFunction('instanceof', [$this, 'isInstanceOf'])];
    }

    public function monthFilter($number)
    {
        $months = [1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        return isset($months[$number]) ? $months[$number] : $number;
    }

    public function absenceFilter($number)
    {
        return EnfanceData::getAbsenceTxt($number);
    }

    public function isInstanceOf($var, $instance)
    {
        $reflexionClass = new \ReflectionClass($instance);

        return $reflexionClass->isInstance($var);
    }
}
