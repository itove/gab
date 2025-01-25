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
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'addSchool',
    description: 'Add a short description for your command',
)]
class AddSchoolCommand extends Command
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
'松滋市划子嘴初级中学',
'松滋市高成初级中学',
'松滋市实验幼儿园',
'松滋市划子嘴幼儿园',
'松滋市机关幼儿园',
'松滋市高成幼儿园',
'松滋市黄杰小学附属幼儿园',
'实验小学附属幼儿园',
'松滋市特殊教育学校',
'松滋市育苗学校',
'松滋市乐乡街道金松小学',
'松滋市乐乡街道车阳河小学',
'松滋市乐乡街道麻水小学',
'松滋市乐乡街道中心幼儿园',
'松滋市乐乡街道碾盘幼儿园',
'松滋市新江口街道阳光实验幼儿园',
'松滋市新江口街道天天向上幼儿园',
'松滋市新街口街道东方实验幼儿园',
'松滋巿新江口街道灯泡厂幼儿园',
'松滋市新江口街道快乐天使幼儿园',
'松滋市新江口街道小天使幼儿园',
'松滋市新江口街道木天河幼儿园',
'松滋市新江口街道星光幼儿园',
'松滋市新江口街道爱心幼儿园',
'松滋市新江口街道桥东幼儿园',
'杨林市镇初级中学',
'杨林市镇杨林市小学',
'杨林市镇大河北小学',
'杨林市镇台山小学',
'杨林市镇小太阳实验幼儿园',
'杨林市镇中心幼儿园',
'杨林市镇台山幼儿园',
'刘家场镇刘家场初级中学',
'刘家场镇庆贺寺初级中学',
'刘家场镇刘家场小学',
'刘家场镇贺炳炎小学',
'刘家场镇官渡坪小学',
'刘家场镇付家坪小学',
'刘家场镇青坪小学',
'刘家场镇桃树小学',
'刘家场镇观音淌小学',
'刘家场镇机关幼儿园',
'刘家场镇机关幼儿园桃分园',
'刘家场镇启明星幼儿园',
'刘家场镇庆贺寺幼儿园',
'刘家场镇青坪中心幼儿园',
'刘家场镇官渡幼儿园',
'刘家场镇龙潭桥幼儿园',
'松滋市纸厂河镇初级中学',
'松滋市纸厂河镇水府小学',
'纸厂河镇纸厂河小学',
'纸厂河镇金羊山小学',
'纸厂河镇万福幼儿园',
'纸厂河镇金羊山幼儿园',
'纸厂河镇陈家场幼儿园',
'纸厂河镇中心幼儿园',
'松滋市陈店镇陈店初级中学',
'松滋市陈店镇陈店小学',
'松滋市陈店镇桃岭小学',
'松滋市陈店镇陈店小学观岳校区',
'松滋市陈店镇陈店小学马峪河校区',
'松滋市陈店镇中心幼儿园',
'松滋市南海镇南海初级中学',
'松滋市南海镇新垱学校',
'松滋市南海镇南海小学',
'松滋市南海镇东湖小学',
'南海镇小南海幼儿园',
'南海镇启智幼儿园',
'南海镇东湖幼儿园',
'南海镇新垱幼儿园',
'南海镇文家铺幼儿园',
'南海镇中心幼儿园',
'松滋市卸甲坪乡镇泰民族学校',
'松滋市卸甲坪乡土家族乡明德小学',
'松滋市卸甲坪乡土家族乡曲尺河小学',
'松滋市卸甲坪乡土家族乡利民幼儿园',
'松滋市卸甲坪乡土家族乡中心幼儿园',
        ];

        foreach ($ss as $s) {
        $user = new School();
        $user->setName($s);
        $user->setProvince('湖北省');
        $user->setCity('荆州市');
        $user->setArea('松滋市');
        $this->em->persist($user);
        }
        $this->em->flush();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
