<?php

namespace AppBundle\Command;

use AppBundle\C;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RenderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:render:run');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templating = $this->getContainer()->get('templating');
        $root = $this->getContainer()->get('kernel')->getRootDir();
        file_put_contents(
            $root.'/../web/index.html',
            $templating->render('AppBundle:Default:index.html.twig', [
            'subways' => $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository(C::REPO_SUBWAY)->findAll()
        ]));
    }
}
