<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 16/08/18
 * Time: 17:23
 */

namespace AcMarche\Mercredi\Commun\Utils;

class DateService
{
    /**
     * @param string $date "01/08/2018"
     * @return \DatePeriod
     * @throws \Exception
     */
    public function getDateIntervale(string $date)
    {
        $begin = \DateTimeImmutable::createFromFormat('d/m/Y', $date);
        $end = $begin->modify('last day of this month');
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');

        return new \DatePeriod($begin, $interval, $end);
    }

    /**
     * Verifie si le jour où on reserve,
     * la date de presence choisie n'est pas plus tard
     * que la veille a 12h00
     * Et pour les jours journees pedagogiques c'est une semaine
     * @param \DateTime $datePresence
     * @param null $today
     * @return bool
     */
    public function checkDate(\DateTimeInterface $datePresence, $today = null)
    {
        if (!$today) {
            $today = new \DateTime();
        }

        $cloneToday = clone $today;
        $todayPlusUneSemaine = $cloneToday->modify('+1 week');

        /**
         * Si journee pedagogique
         */
        if ($datePresence->format('N') != 3) {
            if ($todayPlusUneSemaine->format('Y-m-d') > $datePresence->format('Y-m-d')) {
                return false;
            }

            return true;
        }

        /**
         * La date de la presence est plus vieille que aujourd'hui
         */
        if ($today->format('Y-m-d') > $datePresence->format('Y-m-d')) {
            return false;
        }

        /**
         * si jour de garde egale aujourd'hui
         * trop tard
         */
        if ($today->format('Y-m-d') == $datePresence->format('Y-m-d')) {
            return false;
        }

        /**
         * Si on est un mardi la veille !
         * alors il faut qu'on soit max mardi 12h00
         * si on reserve un mardi 6 pour un admin 7
         */
        if ($today->format('N') == 2) {
            $lendemain = clone $today;
            $lendemain = $lendemain->modify('+1 day');
            //la veille ?
            if ($lendemain->format('d-m-Y') == $datePresence->format('d-m-Y')) {
                //si après 10h
                $heure = (int)$today->format('G');
                $minute = (int)$today->format('i');
                if ($heure > 10) {
                    return false;
                }
                if ($heure == 10) {
                    //si après 10h02
                    if ($minute > 02) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getFr(\DateTime $date, $jourFirst = false) {
        $jour = $date->format('D');
        $jourFr = $this->getDayFr($jour);

        if ($jourFirst) {
            return $jourFr . ' ' . $date->format('d-m-Y');
        }

        return $date->format('d-m-Y') . " " . $jourFr;
    }

    public function getDayFr($jour)
    {
        switch ($jour) {
            case 'Mon':
                $jourFr = 'Lundi';
                break;
            case 'Tue':
                $jourFr = 'Mardi';
                break;
            case 'Wed':
                $jourFr = 'Mercredi';
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

        return $jourFr;
    }

}