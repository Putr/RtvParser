<?php

namespace Putr\Cli\RtvSloParserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Program
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Putr\Cli\RtvSloParserBundle\Entity\ProgramRepository")
 */
class Program
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
     * @ORM\OneToMany(targetEntity="Video", mappedBy="program")
     **/
    private $videos;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="query_url", type="string", length=255)
     */
    private $queryUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="img_link", type="string", length=255)
     */
    private $imgLink;

    /**
     * @var array
     *
     * @ORM\Column(name="scheduled", type="json_array")
     */
    private $scheduled;




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
     * Set title
     *
     * @param string $title
     * @return Program
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Program
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set imgLink
     *
     * @param string $imgLink
     * @return Program
     */
    public function setImgLink($imgLink)
    {
        $this->imgLink = $imgLink;
    
        return $this;
    }

    /**
     * Get imgLink
     *
     * @return string 
     */
    public function getImgLink()
    {
        return $this->imgLink;
    }

    /**
     * Set scheduled
     *
     * @param array $scheduled
     * @return Program
     */
    public function setScheduled($scheduled)
    {
        $this->scheduled = $scheduled;
    
        return $this;
    }

    /**
     * Get scheduled
     *
     * @return array 
     */
    public function getScheduled()
    {
        return $this->scheduled;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->videos = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add videos
     *
     * @param \Putr\Cli\RtvSloParserBundle\Entity\Video $videos
     * @return Program
     */
    public function addVideo(\Putr\Cli\RtvSloParserBundle\Entity\Video $videos)
    {
        $this->videos[] = $videos;
    
        return $this;
    }

    /**
     * Remove videos
     *
     * @param \Putr\Cli\RtvSloParserBundle\Entity\Video $videos
     */
    public function removeVideo(\Putr\Cli\RtvSloParserBundle\Entity\Video $videos)
    {
        $this->videos->removeElement($videos);
    }

    /**
     * Get videos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * Set queryUrl
     *
     * @param string $queryUrl
     * @return Program
     */
    public function setQueryUrl($queryUrl)
    {
        $this->queryUrl = $queryUrl;
    
        return $this;
    }

    /**
     * Get queryUrl
     *
     * @return string 
     */
    public function getQueryUrl()
    {
        return $this->queryUrl;
    }
}