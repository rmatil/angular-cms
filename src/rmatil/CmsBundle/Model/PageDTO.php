<?php


namespace rmatil\CmsBundle\Model;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\Type;

class PageDTO {

    /**
     * Id of the page
     *
     * @Type("integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * Url name for the page
     *
     * @Type("string")
     *
     * @var string
     */
    protected $urlName = '';

    /**
     * The category to which the page belongs
     *
     * @Type("rmatil\CmsBundle\Model\PageCategoryDTO")
     * @MaxDepth(1)
     *
     * @var PageCategoryDTO
     */
    protected $category;

    /**
     * The author of this page
     *
     * @Type("rmatil\CmsBundle\Model\UserDTO")
     * @MaxDepth(1)
     *
     * @var UserDTO
     */
    protected $author;

    /**
     * The language of this page
     *
     * @Type("rmatil\CmsBundle\Model\LanguageDTO")
     * @MaxDepth(2)
     *
     * @var LanguageDTO
     */
    protected $language;

    /**
     * Title of the page
     *
     * @Type("string")
     *
     * @var string
     */
    protected $title = '';

    /**
     * Parent page of this page
     *
     * @Type("rmatil\CmsBundle\Model\PageDTO")
     * @MaxDepth(2)
     *
     * @var \rmatil\CmsBundle\Model\PageDTO
     */
    protected $parent;

    /**
     * An array of articles (bidirectional - inverse side)
     *
     * @Type("ArrayCollection<rmatil\CmsBundle\Model\ArticleDTO>")
     * @MaxDepth(2)
     *
     * @var array
     */
    protected $articles;

    /**
     * Indicates whether this page should show
     * its articles as sub-navigation
     *
     * @Type("boolean")
     *
     * @var boolean
     */
    protected $hasSubnavigation = false;

    /**
     * Indicates whether this page is locked
     * for editing or not
     *
     * @Type("rmatil\CmsBundle\Model\UserDTO")
     * @MaxDepth(1)
     *
     * @var UserDTO
     */
    protected $isLockedBy;

    /**
     * Indicates whether the page should be published or not
     *
     * @Type("boolean")
     *
     * @var boolean
     */
    protected $isPublished = false;

    /**
     * DateTime object of the last edit date. May be null
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     *
     * @var DateTime
     */
    protected $lastEditDate;

    /**
     * DateTime object of the creation date. May be null
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     *
     * @var DateTime
     */
    protected $creationDate;

    /**
     * The user groups which is allowed to access this page.
     *
     * @Type("rmatil\CmsBundle\Model\UserGroupDTO")
     * @MaxDepth(1)
     *
     * @var \rmatil\CmsBundle\Model\UserGroupDTO
     */
    protected $allowedUserGroup;

    /**
     * Indicates whether this page should be used as the start page
     *
     * @Type("boolean")
     *
     * @var boolean
     */
    protected $isStartPage = false;

    public function __construct() {
        $this->articles = new ArrayCollection();
        $this->lastEditDate = new DateTime();
        $this->creationDate = new DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUrlName(): string {
        return $this->urlName;
    }

    /**
     * @param string $urlName
     */
    public function setUrlName(string $urlName) {
        $this->urlName = $urlName;
    }

    /**
     * @return PageCategoryDTO
     */
    public function getCategory(): PageCategoryDTO {
        return $this->category;
    }

    /**
     * @param PageCategoryDTO $category
     */
    public function setCategory(PageCategoryDTO $category) {
        $this->category = $category;
    }

    /**
     * @return UserDTO
     */
    public function getAuthor(): UserDTO {
        return $this->author;
    }

    /**
     * @param UserDTO $author
     */
    public function setAuthor(UserDTO $author) {
        $this->author = $author;
    }

    /**
     * @return LanguageDTO
     */
    public function getLanguage(): LanguageDTO {
        return $this->language;
    }

    /**
     * @param LanguageDTO $language
     */
    public function setLanguage(LanguageDTO $language) {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title) {
        $this->title = $title;
    }

    /**
     * @return \rmatil\CmsBundle\Model\PageDTO
     */
    public function getParent(): PageDTO {
        return $this->parent;
    }

    /**
     * @param \rmatil\CmsBundle\Model\PageDTO $parent
     */
    public function setParent(PageDTO $parent) {
        $this->parent = $parent;
    }

    /**
     * @return array
     */
    public function getArticles(): array {
        return $this->articles;
    }

    /**
     * @param ArrayCollection $articles
     */
    public function setArticles(ArrayCollection $articles) {
        $this->articles = $articles;
    }

    /**
     * @return boolean
     */
    public function isHasSubnavigation(): bool {
        return $this->hasSubnavigation;
    }

    /**
     * @param boolean $hasSubnavigation
     */
    public function setHasSubnavigation(bool $hasSubnavigation) {
        $this->hasSubnavigation = $hasSubnavigation;
    }

    /**
     * @return UserDTO
     */
    public function getIsLockedBy(): UserDTO {
        return $this->isLockedBy;
    }

    /**
     * @param UserDTO $isLockedBy
     */
    public function setIsLockedBy(UserDTO $isLockedBy) {
        $this->isLockedBy = $isLockedBy;
    }

    /**
     * @return boolean
     */
    public function isIsPublished(): bool {
        return $this->isPublished;
    }

    /**
     * @param boolean $isPublished
     */
    public function setIsPublished(bool $isPublished) {
        $this->isPublished = $isPublished;
    }

    /**
     * @return \DateTime
     */
    public function getLastEditDate(): \DateTime {
        return $this->lastEditDate;
    }

    /**
     * @param \DateTime $lastEditDate
     */
    public function setLastEditDate(\DateTime $lastEditDate) {
        $this->lastEditDate = $lastEditDate;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate(): \DateTime {
        return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate(\DateTime $creationDate) {
        $this->creationDate = $creationDate;
    }

    /**
     * @return mixed
     */
    public function getAllowedUserGroup() {
        return $this->allowedUserGroup;
    }

    /**
     * @param mixed $allowedUserGroup
     */
    public function setAllowedUserGroup($allowedUserGroup) {
        $this->allowedUserGroup = $allowedUserGroup;
    }

    /**
     * @return boolean
     */
    public function isIsStartPage(): bool {
        return $this->isStartPage;
    }

    /**
     * @param boolean $isStartPage
     */
    public function setIsStartPage(bool $isStartPage) {
        $this->isStartPage = $isStartPage;
    }
}
