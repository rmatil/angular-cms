<?php

namespace rmatil\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\Type;

/**
 * @ORM\Entity
 * @ORM\Table(name="events")
 **/
class Event {

    /**
     * Id of the Event
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
     * The author of this article
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
     * The author of this article
     *
     * @ORM\ManyToOne(targetEntity="Location")
     *
     * @Type("rmatil\CmsBundle\Entity\Location")
     * @MaxDepth(2)
     *
     * @var \rmatil\CmsBundle\Entity\Location
     */
    protected $location;

    /**
     * A file attached to this event
     *
     * @ORM\ManyToOne(targetEntity="File")
     *
     * @Type("rmatil\CmsBundle\Entity\File")
     * @MaxDepth(2)
     *
     * @var \rmatil\CmsBundle\Entity\File
     */
    protected $file;

    /**
     * The name of the event
     *
     * @ORM\Column(type="string")
     *
     * @Type("string")
     *
     * @var string
     */
    protected $name;

    /**
     * Repeat options for event
     *
     * @ORM\ManyToOne(targetEntity="RepeatOption")
     *
     * @Type("rmatil\CmsBundle\Entity\RepeatOption")
     * @MaxDepth(1)
     *
     * @var \rmatil\CmsBundle\Entity\RepeatOption
     */
    protected $repeatOption;

    /**
     * DateTime object of the start date
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     *
     * @var \DateTime
     */
    protected $startDate;

    /**
     * DateTime object of the end date
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     *
     * @var \DateTime
     */
    protected $endDate;

    /**
     * The description of the event
     *
     * @ORM\Column(type="text", nullable=true)
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
     * DateTime object of the last edit date
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
     * The user group which is allowed to access this event.
     *
     * @ORM\ManyToOne(targetEntity="UserGroup")
     *
     * @Type("rmatil\CmsBundle\Entity\UserGroup")
     * @MaxDepth(1)
     *
     * @var \rmatil\CmsBundle\Entity\UserGroup
     */
    protected $allowedUserGroup;

    /**
     * Url name for the event
     *
     * @ORM\Column(type="string")
     *
     * @Type("string")
     *
     * @var string
     */
    protected $urlName;


    /**
     * Gets the The author of this article.
     *
     * @return \rmatil\CmsBundle\Entity\User
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Sets the The author of this article.
     *
     * @param \rmatil\CmsBundle\Entity\User $author the author
     */
    public function setAuthor(User $author = null) {
        $this->author = $author;
    }

    /**
     * Gets the The author of this article.
     *
     * @return \rmatil\CmsBundle\Entity\Location
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Sets the The author of this article.
     *
     * @param \rmatil\CmsBundle\Entity\Location $location the location
     */
    public function setLocation(Location $location = null) {
        $this->location = $location;
    }

    /**
     * Gets a file attached to this event.
     *
     * @return \rmatil\CmsBundle\Entity\File
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Sets a file attached to this event.
     *
     * @param \rmatil\CmsBundle\Entity\File $file the file to attach
     */
    public function setFile(File $file = null) {
        $this->file = $file;
    }

    /**
     * Gets the The name of the event.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the The name of the event.
     *
     * @param string $name the name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the Repeat options for event.
     *
     * @return \rmatil\CmsBundle\Entity\RepeatOption
     */
    public function getRepeatOption() {
        return $this->repeatOption;
    }

    /**
     * Sets the Repeat options for event.
     *
     * @param \rmatil\CmsBundle\Entity\RepeatOption $repeatOption the repeat option
     */
    public function setRepeatOption(RepeatOption $repeatOption = null) {
        $this->repeatOption = $repeatOption;
    }

    /**
     * Gets the DateTime object of the start date.
     *
     * @return \DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * Sets the DateTime object of the start date.
     *
     * @param \DateTime $startDate the start date
     */
    public function setStartDate(\DateTime $startDate = null) {
        $this->startDate = $startDate;
    }

    /**
     * Gets the DateTime object of the end date.
     *
     * @return \DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * Sets the DateTime object of the end date.
     *
     * @param \DateTime $endDate the end date
     */
    public function setEndDate(\DateTime $endDate = null) {
        $this->endDate = $endDate;
    }

    /**
     * Gets the The description of the event.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets the The description of the event.
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
     * Gets the DateTime object of the last edit date.
     *
     * @return \DateTime
     */
    public function getLastEditDate() {
        return $this->lastEditDate;
    }

    /**
     * Sets the DateTime object of the last edit date.
     *
     * @param \DateTime $lastEditDate the last edit date
     */
    public function setLastEditDate(\DateTime $lastEditDate = null) {
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
    public function setCreationDate(\DateTime $creationDate = null) {
        $this->creationDate = $creationDate;
    }

    /**
     * Gets the Id of the Event.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the Event.
     *
     * @param integer $id the id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get the user group which are allowed to access this event
     *
     * @return UserGroup
     */
    public function getAllowedUserGroup() {
        return $this->allowedUserGroup;
    }

    /**
     * Set the user group which is allowed to access this event.
     *
     * @param UserGroup $allowedUserGroup The user group which may access this event
     */
    public function setAllowedUserGroup($allowedUserGroup) {
        $this->allowedUserGroup = $allowedUserGroup;
    }

    /**
     * @return string
     */
    public function getUrlName() {
        return $this->urlName;
    }

    /**
     * @param string $urlName
     */
    public function setUrlName($urlName) {
        $this->urlName = $urlName;
    }
}
