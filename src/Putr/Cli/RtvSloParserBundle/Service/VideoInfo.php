<?php

namespace Putr\Cli\RtvSloParserBundle\Service;

use Putr\Cli\RtvSloParserBundle\Entity\Video;

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
	 * Updates video data in db
	 * 
	 * @param  string $source 
	 * @param  array  $data   
	 * @return array
	 */
	protected function updateData($data, $program) {

		$this->logger->debug("Updating video data");

		if (empty($data)) {
			$this->logger->debug("Data is empty - nothing to update");
			return;
		}

		$repo = $this->getVideoRepo();

		$new  = array();
		foreach ($data as $entry) {

			if (empty($entry["title"])) {
				$entry["title"] = $program->getTitle() . " " . $entry["date"];
			}

			if (empty($entry["program"])) {
				$entry["program"] = $program;
			}

			$prev = $repo->findByLink($entry["link"]);

			if (empty($prev)) {
				$this->addNewVideo($entry);
				$new[] = $entry;
			}
		}

		// if (count($new) > 0) {
		// 	$rss = $this->container->get('video.rss');
		// 	$rss->getFeed('rtv', true);
		// }

		$this->logger->info(sprintf("Found %s new entries.", count($new)));

		return $new;
	}

	/**
	 * Adds new entry to DB
	 * 
	 * @param array $data
	 * @param string
	 */
	protected function addNewVideo($data) {

		$this->logger->debug(sprintf("Adding new entry for date %s and program %s", $data["date"], $data["program"]->getTitle()));

		$repo = $this->getVideoRepo();

		if (
			!isset($data["program"]) &&
			!isset($data["title"]) &&
			!isset($data["date"]) &&
			!isset($data["link"])
			) {
			throw new \InvalidArgumentException(sprintf("Missing parameters! %s", var_export($data, true)));
		}

		$dn = new Video();
		$dn->setProgram($data["program"]);
		$dn->setTitle($data["title"]);
		$dn->setDatePublished(new \DateTime($data["date"]));
		$dn->setLink($data["link"]);
		$dn->setDateAdded(new \DateTime());

		$this->em->persist($dn);
		$this->em->flush();


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