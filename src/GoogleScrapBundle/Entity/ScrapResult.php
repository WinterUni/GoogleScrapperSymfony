<?php namespace GoogleScrapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="GoogleScrapBundle\Repository\ScrapResultRepository")
 * @ORM\Table(name="scrap_result")
 */
class ScrapResult
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Url()
     * @ORM\Column(type="string")
     */
    private $domainName;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $keyWord;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="boolean")
     */
    private $queryStatus;

    /**
     * @ORM\Column(type="datetime")
     */
    private $queryDate;

    public function __construct()
    {
        $this->queryDate = new \DateTime();
        $this->queryStatus = false;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDomainName()
    {
        return $this->domainName;
    }

    /**
     * @param string $domainName
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;
    }

    /**
     * @return string
     */
    public function getKeyWord()
    {
        return $this->keyWord;
    }

    /**
     * @param string $keyWord
     */
    public function setKeyWord($keyWord)
    {
        $this->keyWord = $keyWord;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return bool
     */
    public function getQueryStatus()
    {
        return $this->queryStatus;
    }

    /**
     * @param mixed $queryStatus
     */
    public function setQueryStatus($queryStatus)
    {
        $this->queryStatus = $queryStatus;
    }

    /**
     * @return string
     */
    public function getQueryDate()
    {
        return $this->queryDate;
    }

    /**
     * @param string $queryDate
     */
    public function setQueryDate($queryDate)
    {
        $this->queryDate = $queryDate;
    }
}