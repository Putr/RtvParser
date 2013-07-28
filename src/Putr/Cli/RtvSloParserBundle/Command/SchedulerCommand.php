<?php

namespace Putr\Cli\RtvSloParserBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;


class SchedulerCommand extends Command {

	protected function configure() {
		$this
			->setName('scraper:scheduler')
			->setDescription('Scheduler - generates tasks')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

    $this->container = $this->getApplication()->getKernel()->getContainer();
    $this->logger    = $this->container->get('logger');

    $redis = $this->container->get('snc_redis.default');

    if (empty($redis)) {
      die("Where the fuck is redis?");
    }

    $this->logger->info("Starting scheduler.");
    $output->writeln("<info>Starting scheduler</info>");

    $today        = new \DateTime();
    $today        = $today->format('d.m.Y');
    $dayOfTheWeek = date('w'); // 0 - sunday ... 6 - saturday
    
    $degMsg = "Loading all programs";
    $this->logger->info($degMsg);
    $output->writeln(sprintf("<info>%s</info>", $degMsg));

    $programs_all = $this->getProgramRepo()->findAll();

    $degMsg = sprintf("Found %s programs.", count($programs_all));
    $this->logger->info($degMsg);
    $output->writeln(sprintf("<info>%s</info>", $degMsg));

    foreach ($programs_all as $program) {

      $sch = $program->getScheduled();

      if (isset($sch[$dayOfTheWeek])) {
        $degMsg = sprintf("Found program to be scheduled: %s", $program->getTitle());
        $this->logger->info($degMsg);
        $output->writeln(sprintf("<info>%s</info>", $degMsg));

        $key = 'sch:' . $program->getId();    

        if($redis->exists($key)) {
          $rdata = $redis->get($key);
          $rdata = json_decode($rdata);

          $tmp_sch = array();
          foreach ($rdata->targetDays as $tmp_key => $tmp_value) {
            $tmp_sch[$tmp_key] = $tmp_value;
          }
          $rdata->targetDays = $tmp_sch;
          unset($tmp_sch, $tmp_key, $tmp_value);

          $degMsg = sprintf("Key (%s) already exsists. Retriving data", $key);
          $this->logger->info($degMsg, array("output" => $rdata));
          $output->writeln(sprintf("<info>%s</info>", $degMsg));

        } else {
          $rdata             = new \stdClass();
          $rdata->url        = $program->getQueryUrl();
          $rdata->programId  = $program->getId();
          $rdata->targetDays = array();
        }

        if (!isset($rdata->targetDays[$today])) {
          $rdata->targetDays[$today] = $sch[$dayOfTheWeek];

          $redis->set($key, json_encode($rdata));

          $degMsg = sprintf("Adding key (%s) for today at %s", $key, $sch[$dayOfTheWeek]);
          $this->logger->info($degMsg, array("output" => $rdata));
          $output->writeln(sprintf("<info>%s</info>", $degMsg));
        }

      }

    }

  }

  /**
   * Returns Doctrine repo and sets doctrine entity maneger
   * 
   */
  protected function getVideoRepo() {
    if (empty($this->repo)) {
      $this->em    = $this->container->get('doctrine')->getManager();
      $this->repo  = $this->em->getRepository('Putr\Cli\RtvSloParserBundle\Entity\Video');
    }
    return $this->repo;
  }

  protected function getProgramRepo() {
    if (empty($this->programRepo)) {
      $this->em           = $this->container->get('doctrine')->getManager();
      $this->programRepo  = $this->em->getRepository('Putr\Cli\RtvSloParserBundle\Entity\Program');
    }
    return $this->programRepo;
  }

}