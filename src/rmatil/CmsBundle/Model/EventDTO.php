<?php


namespace rmatil\CmsBundle\Model;


use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\Type;

class EventDTO {

    /**
     * Id of the Event
     *
     * @Type("integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * The author of this article
     *
     * @Type("rmatil\CmsBundle\Model\UserDTO")
     * @MaxDepth(1)
     *
     * @var \rmatil\CmsBundle\Model\UserDTO
     */
    protected $author;

    /**
     * The author of this article
     *
     * @Type("rmatil\CmsBundle\Model\LocationDTO")
     * @MaxDepth(2)
     *
     * @var \rmatil\CmsBundle\Model\LocationDTO
     */
    protected $location;

    /**
     * A file attached to this event
     *
     * @Type("rmatil\CmsBundle\Model\FileDTO")
     * @MaxDepth(2)
     *
     * @var \rmatil\CmsBundle\Model\FileDTO
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
     * DateTime object of the start date
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     *
     * @var \DateTime
     */
    protected $startDate;

    /**
     * DateTime object of the end date
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     *
     * @var \DateTime
     */
    protected $endDate;

    /**
     * The description of the event
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
     * @Type("rmatil\CmsBundle\Model\UserDTO")
     * @MaxDepth(1)
     *
     * @var \rmatil\CmsBundle\Model\UserDTO
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
     * All user groups which are allowed to access this event.
     *
     * THIS IS THE INVERSE SIDE. CORRESPONDING RELATION IN USERGROUP MUST BE UPDATED MANUALLY
     * @see \rmatil\CmsBundle\Entity\UserGroup::$accessiblePages
     * @link http://docs.doctrine-project.org/en/latest/reference/working-with-associations.html#working-with-associations
     *
     * @ORM\ManyToMany(targetEntity="UserGroup", mappedBy="accessibleEvents")
     * @ORM\JoinTable(name="usergroup_events")
     *
     * @Type("ArrayCollection<rmatil\CmsBundle\Model\UserGroupDTO>")
     * @MaxDepth(2)
     *
     * @var ArrayCollection[rmatil\CmsBundle\Model\UserGroupDTO]
     */
    protected $allowedUserGroups;

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
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return \rmatil\CmsBundle\Model\UserDTO
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * @param \rmatil\CmsBundle\Model\UserDTO $author
     */
    public function setAuthor($author) {
        $this->author = $author;
    }

    /**
     * @return \rmatil\CmsBundle\Model\LocationDTO
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @param \rmatil\CmsBundle\Model\LocationDTO $location
     */
    public function setLocation($location) {
        $this->location = $location;
    }

    /**
     * @return \rmatil\CmsBundle\Model\FileDTO
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @param \rmatil\CmsBundle\Model\FileDTO $file
     */
    public function setFile($file) {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return \rmatil\CmsBundle\Model\UserDTO
     */
    public function getIsLockedBy() {
        return $this->isLockedBy;
    }

    /**
     * @param \rmatil\CmsBundle\Model\UserDTO $isLockedBy
     */
    public function setIsLockedBy($isLockedBy) {
        $this->isLockedBy = $isLockedBy;
    }

    /**
     * @return \DateTime
     */
    public function getLastEditDate() {
        return $this->lastEditDate;
    }

    /**
     * @param \DateTime $lastEditDate
     */
    public function setLastEditDate($lastEditDate) {
        $this->lastEditDate = $lastEditDate;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAllowedUserGroups() {
        return $this->allowedUserGroups;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $allowedUserGroups
     */
    public function setAllowedUserGroups($allowedUserGroups) {
        $this->allowedUserGroups = $allowedUserGroups;
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
