<?php

namespace rmatil\cms\Entities;

use JMS\Serializer\Annotation\Type;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @Type("integer")
     * 
     * @var integer
     */
    protected $id;

    /**
     * Url name for the page
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $urlName;

    /**
     * The category to which the page belongs 
     *
     * @ORM\ManyToOne(targetEntity="PageCategory", cascade="persist")
     *
     * @Type("rmatil\cms\Entities\PageCategory")
     * 
     * @var \rmatil\cms\Entities\PageCategory
     */
    protected $category;

     /**
     * The author of this page
     *
     * @ORM\ManyToOne(targetEntity="User", cascade="persist")
     *
     * @Type("rmatil\cms\Entities\User")
     * 
     * @var \rmatil\cms\Entities\User
     */
    protected $author;

    /**
     * The language of this page
     *
     * @ORM\ManyToOne(targetEntity="Language", cascade="persist")
     *
     * @Type("rmatil\cms\Entities\Language")
     * 
     * @var \rmatil\cms\Entities\Language
     */
    protected $language;

    /**
     * Title of the page
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $title;

    /**
     * Parentpage of this page
     * 
     * @ORM\ManyToOne(targetEntity="Page", cascade="persist")
     *
     * @Type("rmatil\cms\Entities\Page")
     * 
     * @var \rmatil\cms\Entities\Page
     */
    protected $parent;

    /**
     * An array of articles (bidirectional - inverse side)
     *
     * @ORM\OneToMany(targetEntity="Article", mappedBy="page")
     *
     * @Type("ArrayCollection<rmatil\cms\Entities\Article>")
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
     * @Type("boolean")
     * 
     * @var boolean
     */
    protected $hasSubnavigation;

    /**
     * Indicates whether this page is locked 
     * for editing or not
     * 
     * @ORM\ManyToOne(targetEntity="User", cascade="persist")
     *
     * @Type("rmatil\cms\Entities\User")
     * 
     * @var \rmatil\cms\Entities\User
     */
    protected $isLockedBy;

    /**
     * Indicates whether the page should be published or not
     * 
     * @ORM\Column(type="boolean")
     *
     * @Type("boolean")
     * 
     * @var boolean
     */
    protected $isPublic;

    /**
     * DateTime object of the last edit date. May be null
     * 
     * @ORM\Column(type="datetime")
     *
     * @Type("DateTime")
     * 
     * @var \DateTime
     */
    protected $lastEditDate;

    /**
     * DateTime object of the creation date. May be null
     * 
     * @ORM\Column(type="datetime")
     *
     * @Type("DateTime")
     * 
     * @var \DateTime
     */
    protected $creationDate;

    /**
     * All user groups which are allowed to access this page
     *
     * @ORM\ManyToMany(targetEntity="UserGroup", inversedBy="pages")
     * @ORM\JoinTable(name="usergroup_pages")
     *
     * @Type("ArrayCollection<rmatil\cms\Entities\UserGroup>")
     *
     * @var ArrayCollection[rmatil\cms\Entities\UserGroup]
     */
    protected $allowedUserGroups;

    /**
     * Indicates whether this page should be used as the start page
     *
     * @ORM\Column(type="boolean")
     *
     * @Type("boolean")
     *
     * @var boolean
     */
    protected $isStartPage;


    public function __construct() {
        $this->articles = new ArrayCollection();
        $this->allowedUserGroups = new ArrayCollection();
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
     * @return \rmatil\cms\Entities\PageCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Sets the The category to which the page belongs.
     *
     * @param \rmatil\cms\Entities\PageCategory $category the category
     */
    public function setCategory(PageCategory $category = null) {
        $this->category = $category;
    }

    /**
     * Gets the The author of this page.
     *
     * @return \rmatil\cms\Entities\User
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Sets the The author of this page.
     *
     * @param \rmatil\cms\Entities\User $author the author
     */
    public function setAuthor(User $author = null) {
        $this->author = $author;
    }

    /**
     * Gets the The language of this page.
     *
     * @return \rmatil\cms\Entities\Language
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Sets the The language of this page.
     *
     * @param \rmatil\cms\Entities\Language $language the language
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
     * @return \rmatil\cms\Entities\Page
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Sets the Parentpage of this page.
     *
     * @param \rmatil\cms\Entities\Page $parent the parent
     */
    public function setParent(Page $parent = null) {
        $this->parent = $parent;
    }

    /**
     * Gets the An array of articles (bidirectional - inverse side).
     *
     * @return array
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
     * @return \rmatil\cms\Entities\User
     */
    public function getIsLockedBy() {
        return $this->isLockedBy;
    }

    /**
     * Sets the Indicates whether this page is locked for editing or not.
     *
     * @param \rmatil\cms\Entities\User $isLockedBy the is locked
     */
    public function setIsLockedBy(User $isLockedBy = null) {
        $this->isLockedBy = $isLockedBy;
    }

    /**
     * Gets the Indicates whether the page should be published or not.
     *
     * @return boolean
     */
    public function getIsPublic() {
        return $this->isPublic;
    }

    /**
     * Sets the Indicates whether the page should be published or not.
     *
     * @param boolean $isPublic the is public
     */
    public function setIsPublic($isPublic) {
        $this->isPublic = $isPublic;
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
     * Gets all user groups which are allowed to access this page
     *
     * @return ArrayCollection
     */
    public function getAllowedUserGroups() {
        return $this->allowedUserGroups;
    }

    /**
     * Sets all user groups which are allowed to access this page
     *
     * @param ArrayCollection $allowedUserGroups
     */
    public function setAllowedUserGroups($allowedUserGroups) {
        $this->allowedUserGroups = $allowedUserGroups;
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

    /**
     * Updates this object with the values of the given page
     * 
     * @param  Page   $page The page with the values to use
     */
    public function update(Page $page) {
        $this->setArticles($page->getArticles());
        $this->setAuthor($page->getAuthor());
        $this->setCategory($page->getCategory());
        $this->setId($page->getId());
        $this->setLanguage($page->getLanguage());
        $this->setParent($page->getParent());
        $this->setTitle($page->getTitle());
        $this->setCreationDate($page->getCreationDate());
        $this->setHasSubnavigation($page->getHasSubnavigation());
        $this->setIsPublic($page->getIsPublic());
        $this->setUrlName($page->getUrlName());
        $this->setIsLockedBy($page->getIsLockedBy());
        $this->setLastEditDate($page->getLastEditDate());
        $this->setAllowedUserGroups($page->getAllowedUserGroups());
        $this->setIsStartPage($page->getIsStartPage());
    }
}









