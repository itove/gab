<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\School;
use App\Entity\Stage;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'setStages',
    description: 'Add a short description for your command',
)]
class SetStagesCommand extends Command
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
        $ss = [
'七年级、八年级、九年级',
'七年级、八年级、九年级',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'一年级、二年级、三年级、四年级、五年级、六年级、初一、初二、初三',
'一年级、二年级、三年级、四年级、五年级、六年级、初一、初二、初三、高一、高二、高三',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'七年级、八年级、九年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'七年级、八年级、九年级',
'七年级、八年级、九年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'七年级、八年级、九年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'七年级、八年级、九年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'小班、中班、大班',
'七年级、八年级、九年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'小班、中班、大班',
'一年级、二年级、三年级、四年级、五年级、六年级、七年级、八年级、九年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'一年级、二年级、三年级、四年级、五年级、六年级',
'小班、中班、大班',
'小班、中班、大班',
        ];
        
        foreach ($ss as $k => $v) {
            echo $k;
            echo $v;
            $school = $this->em->getRepository(School::class)->find($k+2);
            if (str_contains($v, '小班') ) {
                $stage = $this->em->getRepository(Stage::class)->find(1);
                $school->addStage($stage);
            }
            if (str_contains($v, '一年级') ) {
                $stage = $this->em->getRepository(Stage::class)->find(2);
                $school->addStage($stage);
            }
            if (str_contains($v, '七年级') ) {
                $stage = $this->em->getRepository(Stage::class)->find(3);
                $school->addStage($stage);
            }
            if (str_contains($v, '初一') ) {
                $stage = $this->em->getRepository(Stage::class)->find(4);
                $school->addStage($stage);
            }
            if (str_contains($v, '高一') ) {
                $stage = $this->em->getRepository(Stage::class)->find(5);
                $school->addStage($stage);
            }
        }
        $this->em->flush();
        
        ;

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
