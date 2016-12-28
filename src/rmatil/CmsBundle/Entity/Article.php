<?php

namespace rmatil\CmsBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use rmatil\CmsBundle\Security\IRestrictable;

/**
 * @ORM\Entity
 * @ORM\Table(name="articles")
 **/
class Article implements IRestrictable {

    /**
     * Id of the article
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var integer
     */
    protected $id;

    /**
     * Url name for the article
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $urlName;

    /**
     * The category to which the article belongs
     *
     * @ORM\ManyToOne(targetEntity="ArticleCategory", cascade="persist")
     *
     * @var \rmatil\CmsBundle\Entity\ArticleCategory
     */
    protected $category;

    /**
     * The author of this article
     *
     * @ORM\ManyToOne(targetEntity="User", cascade="persist")
     *
     * @var \rmatil\CmsBundle\Entity\User
     */
    protected $author;

    /**
     * The language of this article
     *
     * @ORM\ManyToOne(targetEntity="Language", cascade="persist")
     *
     * @var \rmatil\CmsBundle\Entity\Language
     */
    protected $language;

    /**
     * Title of the article
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $title;

    /**
     * Body of the article
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $content = '';

    /**
     * DateTime object of the last edit date
     * May be null
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $lastEditDate;

    /**
     * DateTime object of the creation date
     * May be null
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $creationDate;

    /**
     * Indicates whether the article should be published or not
     *
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $isPublished = false;

    /**
     * Page to which this article belongs
     *
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="articles")
     *
     * @var \rmatil\CmsBundle\Entity\Page
     */
    protected $page;

    /**
     * All user groups which are allowed to access this article
     *
     * @ORM\ManyToOne(targetEntity="UserGroup")
     *
     * @var \rmatil\CmsBundle\Entity\UserGroup
     */
    protected $allowedUserGroup;


    public function __construct() {
        $this->content = '';
        $this->creationDate = new DateTime();
        $this->lastEditDate = new DateTime();
        $this->urlName = '';
        $this->title = '';
        $this->isPublished = true;
    }


    /**
     * Gets the Id of the article.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the article.
     *
     * @param integer $id the id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the Url name for the article.
     *
     * @return string
     */
    public function getUrlName() {
        return $this->urlName;
    }

    /**
     * Sets the Url name for the article.
     *
     * @param string $urlName the article url name
     */
    public function setUrlName($urlName) {
        $this->urlName = $urlName;
    }

    /**
     * Gets the The category to which the article belongs.
     *
     * @return \rmatil\CmsBundle\Entity\ArticleCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Sets the The category to which the article belongs.
     *
     * @param \rmatil\CmsBundle\Entity\ArticleCategory $category the article category
     */
    public function setCategory(ArticleCategory $category = null) {
        $this->category = $category;
    }

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
     * Gets the The languate of this article.
     *
     * @return \rmatil\CmsBundle\Entity\Language
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Sets the The languate of this article.
     *
     * @param \rmatil\CmsBundle\Entity\Language $language the language
     */
    public function setLanguage(Language $language = null) {
        $this->language = $language;
    }

    /**
     * Gets the Title of the article.
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the Title of the article.
     *
     * @param string $title the title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Gets the Body of the article.
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Sets the Body of the article.
     *
     * @param string $content the content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * Gets the DateTime object of the last edit date
     * May be null.
     *
     * @return \DateTime
     */
    public function getLastEditDate() {
        return $this->lastEditDate;
    }

    /**
     * Sets the DateTime object of the last edit date
     * May be null.
     *
     * @param \DateTime $lastEditDate the last edit date
     */
    public function setLastEditDate(\DateTime $lastEditDate = null) {
        $this->lastEditDate = $lastEditDate;
    }

    /**
     * Gets the DateTime object of the creation date
     * May be null.
     *
     * @return \DateTime
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * Sets the DateTime object of the creation date
     * May be null.
     *
     * @param \DateTime $creationDate the creation date
     */
    public function setCreationDate(\DateTime $creationDate = null) {
        $this->creationDate = $creationDate;
    }

    /**
     * Gets the Indicates whether the article should be published or not.
     *
     * @return boolean
     */
    public function getIsPublished() {
        return $this->isPublished;
    }

    /**
     * Sets the Indicates whether the article should be published or not.
     *
     * @param boolean $isPublished the is public
     */
    public function setIsPublished($isPublished) {
        $this->isPublished = $isPublished;
    }

    /**
     * Gets the Page to which this article belongs.
     *
     * @return \rmatil\CmsBundle\Entity\Page
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * Sets the Page to which this article belongs.
     *
     * @param \rmatil\CmsBundle\Entity\Page $page the page
     */
    public function setPage(Page $page = null) {
        $this->page = $page;
    }

    /**
     * Get user group which is allowed to access this article
     *
     * @return UserGroup
     */
    public function getAllowedUserGroup() {
        return $this->allowedUserGroup;
    }

    /**
     * Sets the user group which is allowed to access this article.
     *
     * @param UserGroup $allowedUserGroup The user group which may access this article
     */
    public function setAllowedUserGroup(UserGroup $allowedUserGroup = null) {
        $this->allowedUserGroup = $allowedUserGroup;
    }

    public function update(Article $article) {

    }
}
