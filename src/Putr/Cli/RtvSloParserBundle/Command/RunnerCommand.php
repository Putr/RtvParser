<?php

namespace Putr\Cli\RtvSloParserBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;


class RunnerCommand extends Command {

	protected function configure() {
		$this
			->setName('scraper:runner')
			->setDescription('Parses schedule and runs tasks')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

    $this->container = $this->getApplication()->getKernel()->getContainer();
    $this->logger    = $this->container->get('logger');

    $this->logger->info("Starting scheduler.");
    $output->writeln("<info>Starting scheduler</info>");

    $today = new \DateTime();
    $today = $today->modify('-1 day')->format('d.m.Y');

    $msg = array(
      'url' => 'http://ava.rtvslo.si/?c_mod=play&op=search&func=search&form_type=advanced&search_text=&search_media=tv&search_type=34&search_extid=92&search_orderby=date&search_order=desc&search_dateframe=&search_datefrom={from}&search_dateto={to}&page=0',
      'programId' => 1,
      'targetDays' => array( $today )
      );


    $client = $this->container->get('old_sound_rabbit_mq.scraper_scheduler_rpc');

    $client->addRequest(json_encode($msg), 'scraper_worker', 'first_message');

   // $msg["id"]++;
    //$client->addRequest(serialize($msg), 'scraper_worker', 'second_message');
//echo "now get replys";
    $replies = $client->getReplies();

    var_dump($replies);



  }

}