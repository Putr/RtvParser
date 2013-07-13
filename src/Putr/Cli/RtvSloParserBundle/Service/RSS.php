<?php

namespace Putr\Cli\RtvSloParserBundle\Service;

use Putr\Cli\RtvSloParserBundle\Entity\Dnevnik;

class RSS {

	/**
	 * Constructor
	 */
	public function __construct($logger, $container) {

		$this->logger       = $logger;
		$this->container    = $container;

	}

	/**
	 * Retrive Feed data
	 * @param  string  $source
	 * @param  boolean $cacheRebuild 
	 * @return string
	 */
	public function getFeed($source, $cacheRebuild = false){
		$this->logger->debug("Retriving feed", array("source" => $source));

		$cache = $this->container->get('sonata.cache.memcached');

        $key = 'dnevnik.rss.' . $source;

        if (!$cacheRebuild && $cache->has(array($key))) {
			$data = $cache->get(array($key));
			return $data->getData();
		}

		$this->logger->info("Rebuilding cache while getting feed.", array("source" => $source));

        $articles = $this->container->get('doctrine')->getManager()->getRepository('PutrCliRtvSloParserBundle:Dnevnik')->findBy(
        	array("source" => $source),
        	array("date" => "DESC"),
        	14 // Limit
        	);

        switch ($source) {
        	case 'rtv':
        	default:
        		$feed = "rtv";
        		break;
        	
        }
        $feed = $this->container->get('eko_feed.feed.manager')->get($feed);
        $feed->addFromArray($articles);

        $render = $feed->render('rss'); // or 'atom'
        $cache->set(array($key), $render, $ttl = "3600");

        return $render;
	}
}