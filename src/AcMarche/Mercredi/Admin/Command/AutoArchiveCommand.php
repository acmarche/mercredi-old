<?php

namespace AcMarche\Mercredi\Admin\Command;

use AcMarche\Mercredi\Admin\Repository\EnfantTuteurRepository;
use AcMarche\Mercredi\Admin\Repository\TuteurRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AutoArchiveCommand extends Command
{
    /**
     * @var EnfantTuteurRepository
     */
    private $enfantTuteurRepository;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;

    public function __construct(
        EnfantTuteurRepository $enfantTuteurRepository,
        TuteurRepository $tuteurRepository
    ) {
        parent::__construct();
        $this->enfantTuteurRepository = $enfantTuteurRepository;
        $this->tuteurRepository = $tuteurRepository;
    }

    protected function configure()
    {
        $this
            ->setName('mercredi:autoarchive')
            ->setDescription('Auto archive les parents');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->tuteurRepository->findBy(['archive' => 0]) as $tuteur) {
            $enfantTuteurs = $this->enfantTuteurRepository->findBy(['tuteur' => $tuteur]);
            $enfants = array_column($enfantTuteurs, 'enfant', 'id');
            $inActifs = 0;
            foreach ($enfants as $enfant) {
                if ($enfant->getArchive()) {
                    ++$inActifs;
                }
            }
            if (count($enfants) == $inActifs) {
                $tuteur->setArchive(true);
                $output->writeln(($tuteur->getNom().' '.$tuteur->getPrenom()));
            }
        }
    }
}
