<?php

namespace AcMarche\Mercredi\Admin\Twig;

use AcMarche\Mercredi\Admin\Service\EnfanceData;

class MercrediExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('monthtext', array($this, 'monthFilter')),
            new \Twig_SimpleFilter('absencetext', array($this, 'absenceFilter'))
        );
    }

    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('instanceof', array($this, 'isInstanceOf'))];
    }

    public function monthFilter($number)
    {
        $months = array(1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

        return isset($months[$number]) ? $months[$number] : $number;
    }

    public function absenceFilter($number)
    {
        return EnfanceData::getAbsenceTxt($number);
    }

    public function isInstanceOf($var, $instance) {
        $reflexionClass = new \ReflectionClass($instance);
        return $reflexionClass->isInstance($var);
    }

}
