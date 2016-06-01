<?php

namespace rmatil\CmsBundle\Entity;

use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity 
 * @ORM\Table(name="locations")
 **/
class Location {

    /**
     * Id of the location
     * 
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     *
     * @Type("integer")
     * 
     * @var integer
     */
    protected $id;

    /**
     * Author of this location 
     * 
     * @ORM\ManyToOne(targetEntity="User")
     *
     * @Type("rmatil\CmsBundle\Entity\User")
     * @MaxDepth(1)
     *
     * @var \rmatil\CmsBundle\Entity\User
     */
    protected $author;

    /**
     * name of the location
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $name;

    /**
     * address of the location
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $address;

    /**
     * description of the location
     * 
     * @ORM\Column(type="string", nullable=true)
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $description;
    
    /**
     * Indicates whether this article is locked 
     * for editing or not
     * 
     * @ORM\ManyToOne(targetEntity="User", cascade="persist")
     *
     * @Type("rmatil\CmsBundle\Entity\User")
     * @MaxDepth(1)
     * 
     * @var \rmatil\CmsBundle\Entity\User
     */
    protected $isLockedBy;

    /**
     * longitude of the location
     * 
     * @ORM\Column(type="float")
     *
     * @Type("double")
     * 
     * @var float
     */
    protected $longitude;

    /**
     * latitude of the location
     * 
     * @ORM\Column(type="float")
     *
     * @Type("double")
     * 
     * @var float
     */
    protected $latitude;

    /**
     * DateTime object of the last editing date
     * 
     * @ORM\Column(type="datetime")
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     * 
     * @var \DateTime
     */
    protected $lastEditDate;

    /**
     * DateTime object of the creation date
     * 
     * @ORM\Column(type="datetime")
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     * 
     * @var \DateTime
     */
    protected $creationDate;


    /**
     * Gets the Author of this location.
     *
     * @return \rmatil\CmsBundle\Entity\User
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Sets the Author of this location.
     *
     * @param \rmatil\CmsBundle\Entity\User $author the author
     */
    public function setAuthor(User $author = null) {
        $this->author = $author;
    }

    /**
     * Gets the name of the location.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name of the location.
     *
     * @param string $name the name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the address of the location.
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Sets the address of the location.
     *
     * @param string $address the address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * Gets the description of the location.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets the description of the location.
     *
     * @param string $description the description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Gets the user which locks this user
     *
     * @return \rmatil\CmsBundle\Entity\User
     */
    public function getIsLockedBy() {
        return $this->isLockedBy;
    }

    /**
     * Sets the user which locks this article
     *
     * @param \rmatil\CmsBundle\Entity\User $isLockedBy the user which locks the article
     */
    public function setIsLockedBy($isLockedBy) {
        $this->isLockedBy = $isLockedBy;
    }

    /**
     * Gets the longitude of the location.
     *
     * @return float
     */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
     * Sets the longitude of the location.
     *
     * @param float $longitude the longitude
     */
    public function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    /**
     * Gets the latitude of the location.
     *
     * @return float
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * Sets the latitude of the location.
     *
     * @param float $latitude the latitude
     */
    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    /**
     * Gets the DateTime object of the last editing date.
     *
     * @return \DateTime
     */
    public function getLastEditDate() {
        return $this->lastEditDate;
    }

    /**
     * Sets the DateTime object of the last editing date.
     *
     * @param \DateTime $lastEditDate the last edit date
     */
    public function setLastEditDate(\DateTime $lastEditDate) {
        $this->lastEditDate = $lastEditDate;
    }

    /**
     * Gets the DateTime object of the creation date.
     *
     * @return \DateTime
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * Sets the DateTime object of the creation date.
     *
     * @param \DateTime $creationDate the creation date
     */
    public function setCreationDate(\DateTime $creationDate) {
        $this->creationDate = $creationDate;
    }

    public function update(Location $location) {
        $this->setAddress($location->getAddress());
        $this->setDescription($location->getDescription());
        $this->setLatitude($location->getLatitude());
        $this->setLongitude($location->getLongitude());
        $this->setName($location->getName());
        $this->setLastEditDate($location->getLastEditDate());
        $this->setCreationDate($location->getCreationDate());
        $this->setAuthor($location->getAuthor());
    }

    /**
     * Gets the Id of the location.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the location.
     *
     * @param integer $id the id
     */
    public function setId($id) {
        $this->id = $id;
    }
}
