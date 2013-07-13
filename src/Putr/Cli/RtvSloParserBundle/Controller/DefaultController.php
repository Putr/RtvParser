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
