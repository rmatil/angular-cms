<?php

namespace rmatil\cms\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity 
 * @ORM\Table(name="userGroups")
 **/
class UserGroup {

    /**
     * Id of the user
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
     * Name of the usergroup
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $name;

    /**
     * Role of the usergroup
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $role;

    /**
     * @ORM\ManyToMany(targetEntity="Article", mappedBy="userGroups")
     *
     * @Type("ArrayCollection<rmatil\cms\Entities\Article>")
     *
     * @var ArrayCollection[rmatil\cms\Entities\Article]
     */
    protected $accessibleArticles;

    /**
     * @ORM\ManyToMany(targetEntity="Page", mappedBy="userGroups")
     *
     * @Type("ArrayCollection<rmatil\cms\Entities\Page>")
     *
     * @var ArrayCollection[rmatil\cms\Entities\Page]
     */
    protected $accessiblePages;

    /**
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="userGroups")
     *
     * @Type("ArrayCollection<rmatil\cms\Entities\Event>")
     *
     * @var ArrayCollection[rmatil\cms\Entities\Event]
     */
    protected $accessibleEvents;

    public function __construct() {
        $this->accessibleArticles = new ArrayCollection();
        $this->accessiblePages = new ArrayCollection();
        $this->accessibleEvents = new ArrayCollection();
    }

    
     /**
     * Gets the Id of the user.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the user.
     *
     * @param integer $id the id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the Name of the usergroup.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the Name of the usergroup.
     *
     * @param string $name the name
     */
    public function setName($name) {
        $this->name = $name;
    }

     /**
     * Gets the Role of the usergroup.
     *
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Sets the Role of the usergroup.
     *
     * @param string $role the role
     */
    public function setRole($role) {
        $this->role = $role;
    }

    /**
     * Gets all articles which are accessible by this
     * user group
     *
     * @return ArrayCollection[rmatil\cms\Entity\Article]
     */
    public function getAccessibleArticles() {
        return $this->accessibleArticles;
    }

    /**
     * Sets all articles which are accessible by this
     * user group
     *
     * @param ArrayCollection[rmatil\cms\Entity\Article] $accessibleArticles
     */
    public function setAccessibleArticles($accessibleArticles) {
        $this->accessibleArticles = $accessibleArticles;
    }

    /**
     * @return ArrayCollection
     */
    public function getAccessiblePages() {
        return $this->accessiblePages;
    }

    /**
     * @param ArrayCollection $accessiblePages
     */
    public function setAccessiblePages($accessiblePages) {
        $this->accessiblePages = $accessiblePages;
    }

    /**
     * @return ArrayCollection
     */
    public function getAccessibleEvents() {
        return $this->accessibleEvents;
    }

    /**
     * @param ArrayCollection $accessibleEvents
     */
    public function setAccessibleEvents($accessibleEvents) {
        $this->accessibleEvents = $accessibleEvents;
    }

    public function update(UserGroup $userGroup) {
        $this->setName($userGroup->getName());
        $this->setRole($userGroup->getRole());
        $this->setAccessibleArticles($userGroup->getAccessibleArticles());
        $this->setAccessiblePages($userGroup->getAccessiblePages());
        $this->setAccessibleEvents($userGroup->getAccessibleEvents());
    }
}