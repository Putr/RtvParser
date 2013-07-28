<?php

namespace Putr\Cli\RtvSloParserBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ScraperWorkerConsumer implements ConsumerInterface
{

    /**
     * Constructor
     */
    public function __construct($logger, $container) {

        $this->logger       = $logger;
        $this->container    = $container;

    }

    /**
     * Scrape data
     * @param  AMQPMessage $msg 
     *         Requires the following keys in body:
     *         - targetDays (array of dates to look for)
     *         - programId (id of the program to look into)
     *         - url (url to look at)
     *             url has to have {to} and {from} tokens
     * 
     * @return array
     *         Can have the following keys:
     *         - error (if error accured, with message)
     *         - done (array of dates found that match targetDays)
     */
    public function execute(AMQPMessage $msg)
    {

        $body = json_decode($msg->body);

        $this->logger->debug("Worker has recived a job", array("body" => $body));

        if (empty($body)) {
            $this->logger->warn("Message body is empty!");
            return array("error" => "Message body is empty");
        }

        if (!$this->checkMsg($body)) {
            $this->logger->warn("Not all paramaters are set!", array("msg" => $body));
            return array("error" => "Not all paramaters set");
        }

        $targetDays = $body["targetDays"]; // array

        $days     = array();
        $lastDay  = null;
        $firstDay = null;

        foreach ($targetDays as $day) {
            $thisDay = new \DateTime($day);

            $days[] = $thisDay;

            if (is_null($firstDay)) {
                $firstDay = $thisDay;
            }

            if (is_null($lastDay)) {
                $lastDay = $thisDay;
            }

            if ($thisDay->getTimestamp() > $lastDay->getTimestamp()) {
                $lastDay = $thisDay;
            }

            if ($thisDay->getTimestamp() < $firstDay->getTimestamp()) {
                $firstDay = $thisDay;
            }

        }
        
        $rtvVideo = $this->container->get('rtv.scraper');

        $task = array(
                'to'        => $lastDay->format('d.m.Y'),
                'from'      => $firstDay->format('d.m.Y'),
                'programId' => $body["programId"],
                'url'       => $body["url"]
            );

        if (count($targetDays) === 1 || ($lastDay->getTimestamp() - $firstDay->getTimestamp()) < 604800) {
            $task["from"] = $lastDay->modify('-1 week')->format('d.m.Y');
        }

        $data = $rtvVideo->getVideos($task);

        if ($data === false) {
            return array("error" => "RTV server error");
        }

        if (empty($data["changed"])) {
            return array("done" => false);
        }

        $doneDays = array();
        foreach ($targetDays as $tdate) {
            foreach ($data["changed"] as $entity) {
                $cdate = $entity["date"];
                if ($tdate == $cdate) {
                    $doneDays = $cdate;
                    break;
                }
            }
        }
        
        return array("done" => $doneDays);

    }

    protected function checkMsg(&$body) {

        if (!empty($body->programId) && 
            !empty($body->url) && 
            !empty($body->targetDays) ) {
            $body = array(
                'programId' => $body->programId,
                'url' => $body->url,
                'targetDays' => $body->targetDays
                );
            return true;
        }
        return false;
    }
}