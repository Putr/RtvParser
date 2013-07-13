<?php

namespace Putr\Cli\RtvSloParserBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;


class GetDnevnikCommand extends Command {

	protected function configure() {
		$this
			->setName('rtv:dnevnik')
			->setDescription('Scrapes dnevnik videos and publishes to twitter')
      ->addOption(
               'no-twitter',
               null,
               InputOption::VALUE_NONE,
               'Disable tweet sending'
            )
      ->addArgument(
                'fakeDay',
                InputArgument::OPTIONAL,
                'What date do you wish to fake? Any format \DateTime understands'
            )

		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

      $this->container = $this->getApplication()->getKernel()->getContainer();
      $this->logger    = $this->container->get('logger');
      $rtvVideo        = $this->container->get('dnevnik.rtv');
      $twitter         = $this->container->get('dnevnik.twitter');
      
      $this->logger->info("Starting rtv dnevnik process.");
      $output->writeln("<info>Starting rtv dnevnik process</info>");

      $fakeDay = $input->getArgument('fakeDay');
      if (!empty($fakeDay)) {
        $fakeDay = new \DateTime($fakeDay);
        $data = $rtvVideo->getTodaysDnevnik($fakeDay);
      } else {
        $data = $rtvVideo->getTodaysDnevnik();
      }

      if (empty($data)) {
        $this->logger->info("Nothing new @ RTV");
        $output->writeln("<info>Nothing new</info>");
        return;
      }

      if (count($data) !== 1) {
        $this->logger->crit("There is more than 1 new entry!");
        $output->writeln("<error>There is more than 1 new entry!</error>");
        return;
      }

      if (empty($data[0]["date"]) || empty($data[0]["link"])) {
        $this->logger->crit("Entry does not have link or date set!");
        $output->writeln("<error>Entry does not have link or date set!</error>");
      }

      if ($input->getOption('no-twitter')) {
        $output->writeln("<info>NOT sending a tweet.</info>");
        return;
      }

      $message = sprintf("RTV je objavil današnji (%s) dnevnik: %s",
        $data[0]["date"],
        $data[0]["link"]
        );


      $this->logger->info("Pushing message to twitter", array("message" => $message));
      $output->writeln("<info>Pushing message to twitter</info>");
      $output->writeln("<info>$message</info>");
      
      $r = $twitter->pushToTwitter($message);

      if ($r === false) {
        $this->logger->error("Error occured while sending tweet");
        $output->writeln("<error>Error occured while sending tweet</error>");
      }
   }

}