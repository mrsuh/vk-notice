<?php namespace AppBundle\Command;

use AppBundle\C;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class FixturesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:fixtures:load');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = new Parser();
        $cities = $parser->parse(file_get_contents($this->getContainer()->get('kernel')->getRootDir(). '/fixtures/city.yml') );
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo_city = $em->getRepository(C::REPO_CITY);
        $repo_subway = $em->getRepository(C::REPO_SUBWAY);
        $repo_needle = $em->getRepository(C::REPO_NEEDLE);

        foreach($cities as $city_name => $subways) {
            $city = $repo_city->findOneBy(['name' => $city_name]);
            if(!$city) {
                $city = $repo_city->create(['name' => $city_name]);
            }

            foreach($subways as $subway_name => $needles) {
                $subway = $repo_subway->findOneBy(['city' => $city, 'name' => $subway_name]);
                if (!$subway) {
                    $subway = $repo_subway->create(['city' => $city, 'name' => $subway_name]);
                }

                foreach ($needles as $needle_name) {
                    $needle = $repo_needle->findOneBy(['subway' => $subway, 'needle' => $needle_name]);

                    if (!$needle) {
                        $needle = $repo_needle->create(['subway' => $subway, 'needle' => $needle_name]);
                    }
                }
            }
        }
    }
}
