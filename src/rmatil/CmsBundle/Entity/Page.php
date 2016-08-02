<?php

namespace rmatil\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pages")
 **/
class Page {

    /**
     * Id of the page
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var integer
     */
    protected $id;

    /**
     * Url name for the page
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $urlName;

    /**
     * The category to which the page belongs
     *
     * @ORM\ManyToOne(targetEntity="PageCategory", cascade="persist")
     *
     * @var \rmatil\CmsBundle\Entity\PageCategory
     */
    protected $category;

    /**
     * The author of this page
     *
     * @ORM\ManyToOne(targetEntity="User", cascade="persist")
     *
     * @var \rmatil\CmsBundle\Entity\User
     */
    protected $author;

    /**
     * The language of this page
     *
     * @ORM\ManyToOne(targetEntity="Language", cascade="persist")
     *
     * @var \rmatil\CmsBundle\Entity\Language
     */
    protected $language;

    /**
     * Title of the page
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $title;

    /**
     * Parentpage of this page
     *
     * @ORM\ManyToOne(targetEntity="Page", cascade="persist")
     *
     * @var \rmatil\CmsBundle\Entity\Page
     */
    protected $parent;

    /**
     * An array of articles (bidirectional - inverse side)
     *
     * @ORM\OneToMany(targetEntity="Article", mappedBy="page")
     *
     * @var array
     */
    protected $articles;

    /**
     * Indicates whether this page should show
     * its articles as subnavigation
     *
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $hasSubnavigation = false;

    /**
     * Indicates whether this page is locked
     * for editing or not
     *
     * @ORM\ManyToOne(targetEntity="User", cascade="persist")
     *
     * @var \rmatil\CmsBundle\Entity\User
     */
    protected $isLockedBy;

    /**
     * Indicates whether the page should be published or not
     *
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $isPublished = false;

    /**
     * DateTime object of the last edit date. May be null
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $lastEditDate;

    /**
     * DateTime object of the creation date. May be null
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $creationDate;

    /**
     * All user groups which are allowed to access this page.
     *
     * THIS IS THE INVERSE SIDE. CORRESPONDING RELATION IN USERGROUP MUST BE UPDATED MANUALLY
     * @see  \rmatil\CmsBundle\Entity\UserGroup::$accessiblePages
     * @link http://docs.doctrine-project.org/en/latest/reference/working-with-associations.html#working-with-associations
     *
     * @ORM\ManyToOne(targetEntity="UserGroup")
     *
     * @var \rmatil\CmsBundle\Entity\UserGroup
     */
    protected $allowedUserGroup;

    /**
     * Indicates whether this page should be used as the start page
     *
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $isStartPage = false;


    public function __construct() {
        $this->articles = new ArrayCollection();
    }

    /**
     * Gets the Id of the page.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the page.
     *
     * @param integer $id the id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the Url name for the page.
     *
     * @return string
     */
    public function getUrlName() {
        return $this->urlName;
    }

    /**
     * Sets the Url name for the page.
     *
     * @param string $urlName the url name
     */
    public function setUrlName($urlName) {
        $this->urlName = $urlName;
    }

    /**
     * Gets the The category to which the page belongs.
     *
     * @return \rmatil\CmsBundle\Entity\PageCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Sets the The category to which the page belongs.
     *
     * @param \rmatil\CmsBundle\Entity\PageCategory $category the category
     */
    public function setCategory(PageCategory $category = null) {
        $this->category = $category;
    }

    /**
     * Gets the The author of this page.
     *
     * @return \rmatil\CmsBundle\Entity\User
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Sets the The author of this page.
     *
     * @param \rmatil\CmsBundle\Entity\User $author the author
     */
    public function setAuthor(User $author = null) {
        $this->author = $author;
    }

    /**
     * Gets the The language of this page.
     *
     * @return \rmatil\CmsBundle\Entity\Language
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Sets the The language of this page.
     *
     * @param \rmatil\CmsBundle\Entity\Language $language the language
     */
    public function setLanguage(Language $language = null) {
        $this->language = $language;
    }

    /**
     * Gets the Title of the page.
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the Title of the page.
     *
     * @param string $title the title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Gets the Parentpage of this page.
     *
     * @return \rmatil\CmsBundle\Entity\Page
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Sets the Parentpage of this page.
     *
     * @param \rmatil\CmsBundle\Entity\Page $parent the parent
     */
    public function setParent(Page $parent = null) {
        $this->parent = $parent;
    }

    /**
     * Gets the An array of articles (bidirectional - inverse side).
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getArticles() {
        return $this->articles;
    }

    /**
     * Sets the An array of articles (bidirectional - inverse side).
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $articles the articles
     */
    public function setArticles(ArrayCollection $articles = null) {
        $this->articles = $articles;
    }

    /**
     * Gets the Indicates whether this page should show its articles as subnavigation.
     *
     * @return boolean
     */
    public function getHasSubnavigation() {
        return $this->hasSubnavigation;
    }

    /**
     * Sets the Indicates whether this page should show its articles as subnavigation.
     *
     * @param boolean $hasSubnavigation the has subnavigation
     */
    public function setHasSubnavigation($hasSubnavigation) {
        $this->hasSubnavigation = $hasSubnavigation;
    }

    /**
     * Gets the Indicates whether this page is locked for editing or not.
     *
     * @return \rmatil\CmsBundle\Entity\User
     */
    public function getIsLockedBy() {
        return $this->isLockedBy;
    }

    /**
     * Sets the Indicates whether this page is locked for editing or not.
     *
     * @param \rmatil\CmsBundle\Entity\User $isLockedBy the is locked
     */
    public function setIsLockedBy(User $isLockedBy = null) {
        $this->isLockedBy = $isLockedBy;
    }

    /**
     * Gets the Indicates whether the page should be published or not.
     *
     * @return boolean
     */
    public function getIsPublished() {
        return $this->isPublished;
    }

    /**
     * Sets the Indicates whether the page should be published or not.
     *
     * @param boolean $isPublished the is public
     */
    public function setIsPublished($isPublished) {
        $this->isPublished = $isPublished;
    }

    /**
     * Gets the DateTime object of the last edit date. May be null.
     *
     * @return \DateTime
     */
    public function getLastEditDate() {
        return $this->lastEditDate;
    }

    /**
     * Sets the DateTime object of the last edit date. May be null.
     *
     * @param \DateTime $lastEditDate the last edit date
     */
    public function setLastEditDate(\DateTime $lastEditDate = null) {
        $this->lastEditDate = $lastEditDate;
    }

    /**
     * Gets the DateTime object of the creation date. May be null.
     *
     * @return \DateTime
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * Sets the DateTime object of the creation date. May be null.
     *
     * @param \DateTime $creationDate the creation date
     */
    public function setCreationDate(\DateTime $creationDate = null) {
        $this->creationDate = $creationDate;
    }

    /**
     * Get the user group which are allowed to access this page
     *
     * @return UserGroup
     */
    public function getAllowedUserGroup() {
        return $this->allowedUserGroup;
    }

    /**
     * Set the user group which is allowed to access this page
     *
     * @param UserGroup $allowedUserGroup The user group which may access this page
     */
    public function setAllowedUserGroup($allowedUserGroup) {
        $this->allowedUserGroup = $allowedUserGroup;
    }

    /**
     * Gets the indicator whether this page is used as index page
     *
     * @return boolean
     */
    public function getIsStartPage() {
        return $this->isStartPage;
    }

    /**
     * Sets the indicator whether this page is used as index page
     *
     * @param boolean $isStartPage
     */
    public function setIsStartPage($isStartPage) {
        $this->isStartPage = $isStartPage;
    }
}









