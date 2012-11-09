<?php
namespace Kinopoisk\RatingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kinopoisk\RatingBundle\Model\ParseKinopoiskRatingModel;


class ParseKinopoiskCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setDefinition(array())->setDescription('Parse rating from kinopoisk')->setName(
            'cron:rating:parse'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = 'http://www.kinopoisk.ru/top/';
        $parser = new ParseKinopoiskRatingModel($url,$this->getContainer()->get('doctrine'));
        $parser->saveParseValues();
        $logger = $this->getContainer()->get('logger');
        $logger->info('Parse film from kinopoisk date:'.(date( 'd-m-Y H:i:s')));

    }
}