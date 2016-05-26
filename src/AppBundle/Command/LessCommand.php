<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use lessc;

class LessCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:assets:less')
            ->setDescription('Less to css');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $less = new lessc();
        $dir = $this->getContainer()->get('kernel')->getRootDir() . '/../web/style';
        $less->compileFile($dir.'/subscribe.less', $dir.'/subscribe.css');
        $less->compileFile($dir.'/unsubscribe.less', $dir.'/unsubscribe.css');
    }
}
