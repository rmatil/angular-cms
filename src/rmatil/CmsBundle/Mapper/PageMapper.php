<?php


namespace rmatil\CmsBundle\Mapper;


use Doctrine\Common\Collections\ArrayCollection;
use rmatil\CmsBundle\Entity\Page;
use rmatil\CmsBundle\Exception\MapperException;
use rmatil\CmsBundle\Model\PageDTO;

class PageMapper extends AbstractMapper {

    protected $pageCategoryMapper;
    protected $articleMapper;
    protected $languageMapper;
    protected $userGroupMapper;
    protected $userMapper;

    public function __construct(PageCategoryMapper $pageCategoryMapper, ArticleMapper $articleMapper, LanguageMapper $languageMapper, UserGroupMapper $userGroupMapper, UserMapper $userMapper) {
        $this->pageCategoryMapper = $pageCategoryMapper;
        $this->articleMapper = $articleMapper;
        $this->languageMapper = $languageMapper;
        $this->userGroupMapper = $userGroupMapper;
        $this->userMapper = $userMapper;
    }

    public function entityToDto($page) {
        if (null !== $page) {
            return null;
        }

        if ( ! ($page instanceof Page)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', Page::class, get_class($page)));
        }

        $pageDto = new PageDTO();
        $pageDto->setId($page->getId());
        $pageDto->setUrlName($page->getUrlName());

        if (null !== $page->getCategory()) {
            $pageDto->setCategory($this->pageCategoryMapper->entityToDto($page->getCategory()));
        }

        if (null !== $page->getAuthor()) {
            $pageDto->setAuthor($this->userMapper->entityToDto($page->getAuthor()));
        }

        if (null !== $page->getLanguage()) {
            $pageDto->setLanguage($this->languageMapper->entityToDto($page->getLanguage()));
        }

        $pageDto->setTitle($page->getTitle());

        if (null !== $page->getParent()) {
            $pageDto->setParent($this->entityToDto($page->getParent()));
        }

        $articleDtos = new ArrayCollection();
        foreach ($page->getArticles() as $article) {
            $articleDto = $this->articleMapper->entityToDto($article);
            $articleDtos->add($articleDto);
        }
        $pageDto->setArticles($articleDtos);

        $pageDto->setHasSubnavigation($page->getHasSubnavigation());
        $pageDto->setIsLockedBy($this->userMapper->entityToDto($page->getIsLockedBy()));
        $pageDto->setIsPublished($page->getIsPublished());

        if (null !== $page->getLastEditDate()) {
            $pageDto->setLastEditDate($page->getLastEditDate());
        }

        if (null !== $page->getCreationDate()) {
            $pageDto->setCreationDate($page->getCreationDate());
        }

        if (null !== $page->getAllowedUserGroup()) {
            $pageDto->setAllowedUserGroup($this->userGroupMapper->entityToDto($page->getAllowedUserGroup()));
        }

        $pageDto->setIsStartPage($page->getIsStartPage());

        return $pageDto;
    }

    public function dtoToEntity($pageDto) {
        if (null === $pageDto) {
            return null;
        }

        if ( ! ($pageDto instanceof PageDTO)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', PageDTO::class, get_class($pageDto)));
        }

        $page = new Page();
        $page->setId($pageDto->getId());
        $page->setUrlName($pageDto->getUrlName());

        if (null !== $pageDto->getCategory()) {
            $page->setCategory($this->pageCategoryMapper->dtoToEntity($pageDto->getCategory()));
        }

        if (null !== $pageDto->getAuthor()) {
            $page->setAuthor($this->userMapper->dtoToEntity($pageDto->getAuthor()));
        }

        if (null !== $pageDto->getLanguage()) {
            $page->setLanguage($this->languageMapper->dtoToEntity($pageDto->getLanguage()));
        }

        $page->setTitle($pageDto->getTitle());

        if (null !== $pageDto->getParent()) {
            $page->setParent($this->dtoToEntity($pageDto->getParent()));
        }

        $articles = new ArrayCollection();
        foreach ($pageDto->getArticles() as $articleDTO) {
            $article = $this->articleMapper->dtoToEntity($articleDTO);
            $articles->add($article);
        }
        $page->setArticles($articles);

        $page->setHasSubnavigation($pageDto->isHasSubnavigation());
        $page->setIsLockedBy($this->userMapper->dtoToEntity($pageDto->getIsLockedBy()));
        $page->setIsPublished($pageDto->isIsPublished());

        if (null !== $pageDto->getLastEditDate()) {
            $page->setLastEditDate($pageDto->getLastEditDate());
        }

        if (null !== $pageDto->getCreationDate()) {
            $page->setCreationDate($pageDto->getCreationDate());
        }

        if (null !== $pageDto->getAllowedUserGroup()) {
            $page->setAllowedUserGroup($this->userGroupMapper->dtoToEntity($pageDto->getAllowedUserGroup()));
        }

        $page->setIsStartPage($pageDto->isIsStartPage());

        return $page;
    }
}
