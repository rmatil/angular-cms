<?php

namespace rmatil\cms\Entities;

use Doctrine\Common\Collections\ArrayCollection;
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
     * All articles which may be accessed by this user group.
     *
     * THIS IS THE OWNING SIDE.
     * @see \rmatil\cms\Entities\Article::$allowedUserGroups
     * @link http://docs.doctrine-project.org/en/latest/reference/working-with-associations.html#working-with-associations
     *
     * @ORM\ManyToMany(targetEntity="Article", inversedBy="allowedUserGroups")
     *
     * @Type("ArrayCollection<rmatil\cms\Entities\Article>")
     *
     * @var ArrayCollection[rmatil\cms\Entities\Article]
     */
    protected $accessibleArticles;

    /**
     * All pages which may be accessed by this user group.
     *
     * THIS IS THE OWNING SIDE.
     * @see \rmatil\cms\Entities\Page::$allowedUserGroups
     * @link http://docs.doctrine-project.org/en/latest/reference/working-with-associations.html#working-with-associations
     *
     * @ORM\ManyToMany(targetEntity="Page", inversedBy="allowedUserGroups")
     *
     * @Type("ArrayCollection<rmatil\cms\Entities\Page>")
     *
     * @var ArrayCollection[rmatil\cms\Entities\Page]
     */
    protected $accessiblePages;

    /**
     * All events which may be accessed by this user group.
     *
     * THIS IS THE OWNING SIDE.
     * @see \rmatil\cms\Entities\Event::$allowedUserGroups
     * @link http://docs.doctrine-project.org/en/latest/reference/working-with-associations.html#working-with-associations
     *
     * @ORM\ManyToMany(targetEntity="Event", inversedBy="allowedUserGroups")
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
     * Sets all articles which are accessible by this user group.
     * THIS IS THE OWNING SIDE. USE ADD/REMOVE TO PERSIST CHANGES TO INVERSE SIDE TOO:
     *
     * @param ArrayCollection [rmatil\cms\Entity\Article] $accessibleArticles
     */
    public function setAccessibleArticles($accessibleArticles) {
        $this->accessibleArticles = $accessibleArticles;
    }

    /**
     * Adds an accessible article. Updates the given article too.
     * THIS IS THE OWNING SIDE.
     *
     * @param Article $article
     */
    public function addAccessibleArticle(Article $article) {
        $this->accessibleArticles->add($article);
        $article->addAllowedUserGroup($this);
    }

    /**
     * Removes an accessible article. Updates the given article too.
     * THIS IS THE OWNING SIDE.
     *
     * @param Article $article
     */
    public function removeAccessibleArticle(Article $article) {
        $this->accessibleArticles->removeElement($article);
        $article->removeAllowedUserGroup($this);
    }

    /**
     * Gets all accessible pages.
     * THIS IS THE OWNING SIDE.
     *
     * @return ArrayCollection
     */
    public function getAccessiblePages() {
        return $this->accessiblePages;
    }

    /**
     * Sets all accessible pages.
     * THIS IS THE OWNING SIDE. USE ADD/REMOVE TO PERSIST CHANGES TO INVERSE SIDE TOO:
     *
     * @param ArrayCollection $accessiblePages
     */
    public function setAccessiblePages($accessiblePages) {
        $this->accessiblePages = $accessiblePages;
    }

    /**
     * Adds an accessible page. Updates the given page too.
     * THIS IS THE OWNING SIDE.
     *
     * @param Page $page
     */
    public function addAccessiblePage(Page $page) {
        $this->accessiblePages->add($page);
        $page->addAllowedUserGroup($this);
    }

    /**
     * Removes an accessible page. Updates the given page too.
     * THIS IS THE OWNING SIDE.
     *
     * @param Page $page
     */
    public function removeAccessiblePage(Page $page) {
        $this->accessiblePages->removeElement($page);
        $page->removeAllowedUserGroup($this);
    }

    /**
     * Gets all accessible events
     * THIS IS THE OWNING SIDE.
     *
     * @return ArrayCollection
     */
    public function getAccessibleEvents() {
        return $this->accessibleEvents;
    }

    /**
     * Sets all accessible events.
     * THIS IS THE OWNING SIDE. USE ADD/REMOVE TO PERSIST CHANGES TO INVERSE SIDE TOO:
     *
     * @param ArrayCollection $accessibleEvents
     */
    public function setAccessibleEvents($accessibleEvents) {
        $this->accessibleEvents = $accessibleEvents;
    }

    /**
     * Adds an accessible event. Updates the given event too.
     * THIS IS THE OWNING SIDE.
     *
     * @param Event $event
     */
    public function addAccessibleEvent(Event $event) {
        $this->accessibleEvents->add($event);
        $event->addAllowedUserGroup($this);
    }

    /**
     * Removes an accessible event. Updates the given event too.
     * THIS IS THE OWNING SIDE.
     *
     * @param Event $event
     */
    public function removeAccessibleEvent(Event $event) {
        $this->accessibleEvents->removeElement($event);
        $event->removeAllowedUserGroup($this);
    }

    public function update(UserGroup $userGroup) {
        $this->setName($userGroup->getName());
        $this->setRole($userGroup->getRole());
        $this->setAccessibleArticles($userGroup->getAccessibleArticles());
        $this->setAccessiblePages($userGroup->getAccessiblePages());
        $this->setAccessibleEvents($userGroup->getAccessibleEvents());
    }
}