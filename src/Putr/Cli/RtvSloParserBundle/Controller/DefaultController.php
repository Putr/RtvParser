<?php

namespace Putr\Cli\RtvSloParserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
       $msg = array('url' => "nek url");
    $this->container->get('old_sound_rabbit_mq.scraper_scheduler_producer')->publish(serialize($msg));
    var_dump("Sent message"); die();
        return array();
    }

    /**
     * @Template()
     */
    public function feedAction($source)
    {
        $rss = $this->get('dnevnik.rss');

        $feedStructure = $rss->getFeed($source);
        
        return new Response($feedStructure);
    }
}
