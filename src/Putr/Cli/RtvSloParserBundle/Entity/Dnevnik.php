<?php

namespace Putr\Cli\RtvSloParserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eko\FeedBundle\Item\Writer\ItemInterface;

/**
 * Dnevnik
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Dnevnik implements ItemInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=3)
     */
    private $source;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="added_date", type="datetime")
     */
    private $addedDate;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return Dnevnik
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Dnevnik
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Dnevnik
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set addedDate
     *
     * @param \DateTime $addedDate
     * @return Dnevnik
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;
    
        return $this;
    }

    /**
     * Get addedDate
     *
     * @return \DateTime 
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }

    /**
     * This method returns feed item title
     *
     * @return string
     */
    public function getFeedItemTitle() {
        $date = $this->getDate()->format('d.m');
        $day = $this->getDate()->format('w');

        switch ($day) {
            case '0':
                $day = "Nedelja";
                break;
            case '1':
                $day = "Ponedeljek";
                break;
            case '2':
                $day = "Torek";
                break;
            case '3':
                $day = "Sreda";
                break;
            case '4':
                $day = "Četrtek";
                break;
            case '5':
                $day = "Petek";
                break;
            case '6':
                $day = "Sobota";
                break;
            
            default:
                $day = "";
                break;
        }

        switch ($this->source) {
            case 'rtv':
            default:
                $title = sprintf("Dnevnik %s, %s", $day, $date);
                break;
        }

        return $title;
    }

    /**
     * This method returns feed item description (or content)
     *
     * @return string
     */
    public function getFeedItemDescription() {
        return sprintf("Dnevnik z dneva %s", $this->getDate()->format('d.m.Y'));
    }

     /**
     * This method returns feed item URL link
     *
     * @return string
     */
    public function getFeedItemLink() {
        return $this->getUrl();
    }

    /**
     * This method returns item publication date
     *
     * @abstract
     *
     * @return \DateTime
     */
    public function getFeedItemPubDate() {
        return $this->getAddedDate();
    }
}
