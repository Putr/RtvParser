<?php

namespace Putr\Cli\RtvSloParserBundle\Service;

class Twitter {

	/**
	 * Constructor
	 */
	public function __construct($logger, $container) {

		$this->logger       = $logger;
		$this->container    = $container;

	}
	
	/**
	 * Sends a tweet
	 * 
	 * @param  string $message
	 * @return boolean
	 */
	public function pushToTwitter($message) {

		$lib_path = __DIR__ . "/../../../../../vendor/twitteroauth/twitteroauth";

		require_once($lib_path . "/twitteroauth.php");
        require_once($lib_path . "/OAuth.php");

        $consumerKey    = $this->container->getParameter('twitter.dnevnik.consumerKey');
        $consumerSecret = $this->container->getParameter('twitter.dnevnik.consumerSecret');
        $OAuthToken     = $this->container->getParameter('twitter.OAuthToken');
        $OAuthSecret    = $this->container->getParameter('twitter.OAuthSecret');

        $tweet          = new \TwitterOAuth($consumerKey, $consumerSecret, $OAuthToken, $OAuthSecret);
        $tweet->host    = $this->container->getParameter('twitter.host');

        $r = $tweet->post('statuses/update', array('status' => $message));

        return !(isset($r->errors) && count($r->errors));
	}

}