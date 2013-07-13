<?php

namespace Putr\Cli\RtvSloParserBundle\Service;

use Putr\Cli\RtvSloParserBundle\Entity\Dnevnik;

class VideoInfo {

	protected $repo;
	protected $em;

	/**
	 * Constructor
	 */
	public function __construct($logger, $container) {

		$this->logger       = $logger;
		$this->container    = $container;

	}

	/**
	 * Updates dnevnik data in db
	 * 
	 * @param  string $source 
	 * @param  array  $data   
	 * @return array
	 */
	protected function updateData($source, $data) {

		$this->logger->debug("Updating dnevnik data for ". $source);

		if (empty($data)) {
			$this->logger->debug("Data is empty - nothing to update");
			return;
		}

		if (empty($source)) {
			throw new \InvalidArgumentException("Source is not defined");
		}

		$repo = $this->getDnevnikRepo();

		$new  = array();
		foreach ($data as $entry) {

			$prev = $repo->findByUrl($entry["link"]);

			if (empty($prev)) {
				$this->addNewDnevnik($entry, $source);
				$new[] = $entry;
			}
		}

		if (count($new) > 0) {
			$rss = $this->container->get('dnevnik.rss');
			$rss->getFeed('rtv', true);
		}

		$this->logger->info(sprintf("Found %s new entries.", count($new)));

		return $new;
	}

	/**
	 * Adds new entry to DB
	 * 
	 * @param array $data
	 * @param string
	 */
	protected function addNewDnevnik($data, $source) {

		$this->logger->debug(sprintf("Adding new entry for date %s and source %s", $data["date"], $source));

		$repo = $this->getDnevnikRepo();

		$dn = new Dnevnik();
		$dn->setSource($source);
		$dn->setDate(new \DateTime($data["date"]));
		$dn->setUrl($data["link"]);
		$dn->setAddedDate(new \DateTime());

		$this->em->persist($dn);
		$this->em->flush();


	}

	/**
	 * Returns Doctrine repo and sets doctrine entity maneger
	 * 
	 */
	protected function getDnevnikRepo() {
		if (empty($this->repo)) {
			$this->em    = $this->container->get('doctrine')->getManager();
			$this->repo  = $this->em->getRepository('Putr\Cli\RtvSloParserBundle\Entity\Dnevnik');
		}
		return $this->repo;
	}


	/**
	 * Retrives URL
	 *
	 * @source http://www.jonasjohn.de/snippets/php/curl-example.htm
	 */
	protected function curl($url){

		$this->logger->debug("cUrling resource: ". $url);

	    // is cURL installed yet?
	    if (!function_exists('curl_init')){
	        die('Sorry cURL is not installed!');
	    }

	    // OK cool - then let's create a new cURL resource handle
	    $ch = curl_init();

	    // Now set some options (most are optional)

	    // Set URL to download
	    curl_setopt($ch, CURLOPT_URL, $url);

	    // Set a referer
	    //curl_setopt($ch, CURLOPT_REFERER, $url);

	    // User agent
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.12 (KHTML, like Gecko) Maxthon/3.0 Chrome/26.0.1410.43 Safari/535.12");

	    // Include header in result?
	    curl_setopt($ch, CURLOPT_HEADER, 0);

	    // Should cURL return or print out the data? (true = return, false = print)
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    // Timeout in seconds
	    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

	    // Download the given URL, and return output
	    $output = curl_exec($ch);

	    // Close the cURL resource, and free system resources
	    curl_close($ch);

	    $this->logger->debug("Output", array("rawData" => $output));

	    return $output;
	}
}