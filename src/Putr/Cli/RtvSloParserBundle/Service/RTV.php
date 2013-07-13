<?php

namespace Putr\Cli\RtvSloParserBundle\Service;

use Symfony\Component\DomCrawler\Crawler;

class RTV extends VideoInfo {

	public $rootUrl = "http://ava.rtvslo.si";

	const SOURCE = "rtv";

	/**
	 * Constructor
	 */
	public function __construct($logger, $container) {

		$this->logger       = $logger;
		$this->container    = $container;

	}

	/**
	 * Get dnevniks for today
	 * 
	 * @param  boolean|\DateTime $today 
	 *         Used to force retrival of older days
	 * @return array
	 *         New entries
	 */
	public function getTodaysDnevnik($today = false) {
		$this->logger->debug("Retriving todays RTV dnevniks");

		$to = $from = new \DateTime();

		if ($today !== false) {
			$to = $from = $today;
		}

		$data = $this->getDnevnik($to, $from);

		return $data["changed"];
	}

	/**
	 * Retrives data from RTV for a given date span
	 * @param  \DateTime        $to
	 * @param  \DateTime|string $from
	 * @return array
	 */
	public function getDnevnik(\DateTime $to, $from = "") {

		$to = $to->format('d.m.Y');

		if ($from !== "") {
			$from = $from->format('d.m.Y');
		}

		$this->logger->debug(sprintf("Retriving dnevniks between %s and %s", $to, $from));

		$url = sprintf("http://ava.rtvslo.si/?c_mod=play&op=search&func=search&form_type=advanced&search_text=&search_media=tv&search_type=34&search_extid=92&search_orderby=date&search_order=desc&search_dateframe=1w&search_datefrom=%s&search_dateto=%s&page=0",
				$from,
				$to
				);

		$data = $this->getAjaxList($url);

		$changed = $this->updateData(self::SOURCE, $data);

		return array(
			"all" => $data,
			"changed" => $changed
			);

	}

	protected function getAjaxList($url) {

		$this->logger->debug("Retriving list from RTV");

		$html = $this->curl($url);

		if (!is_string($html)) {
			$this->logger->warn("RTV did not return a string");
			exit;
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