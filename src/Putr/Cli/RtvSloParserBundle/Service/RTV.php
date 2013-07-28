<?php

namespace Putr\Cli\RtvSloParserBundle\Service;

use Symfony\Component\DomCrawler\Crawler;
use Putr\Cli\RtvSloParserBundle\Entity\Program;

class RTV extends VideoInfo {

	public $rootUrl = "http://ava.rtvslo.si";

	protected $programRepo;

	/**
	 * Constructor
	 */
	public function __construct($logger, $container) {

		$this->logger       = $logger;
		$this->container    = $container;

	}

	/**
	 * Retrives data from RTV for a given date span
	 * @param  array            $task
	 * 
	 * @return array
	 */
	public function getVideos($task) {

		$to = new \DateTime($task["to"]);
		$from = new \DateTime($task["from"]);

		$to = $to->format('d.m.Y');

		if ($from) {
			$from = $from->format('d.m.Y');
		}

		$program = $this->getProgramRepo()->findOneById($task["programId"]);

		$this->logger->debug(sprintf("Retriving videos between %s and %s for %s", $to, $from, $program->getTitle()));
	
		$url = str_replace(array("{to}", "{from}"), array($to, $from), $task["url"]);

		$data = $this->getAjaxList($url);

		if ($data === false) {
			return false;
		}

		$changed = $this->updateData($data, $program);

		return array(
			"all" => $data,
			"changed" => $changed
			);

	}

	protected function getProgramRepo() {
		if (empty($this->programRepo)) {
			$this->em           = $this->container->get('doctrine')->getManager();
			$this->programRepo  = $this->em->getRepository('Putr\Cli\RtvSloParserBundle\Entity\Program');
		}
		return $this->programRepo;
	}

	protected function getAjaxList($url) {

		$this->logger->debug("Retriving list from RTV");

		$html = $this->curl($url);

		if (!is_string($html)) {
			$this->logger->warn("RTV did not return a string");
			return false;
		}

		$crawler = new Crawler($html);

		$list = $crawler->filter('tr');

		$data = array();
		foreach ($list as $entry) {
			$g = array (
				"link" => $this->rootUrl . $entry->getElementsByTagName("a")->item(0)->getAttribute("href"),
				"date" => $entry->getElementsByTagName("td")->item(1)->nodeValue
				);
			$data[] = $g;
		}

		$this->logger->info(sprintf("Found %s entries", count($data)));

		return $data;

	}
}