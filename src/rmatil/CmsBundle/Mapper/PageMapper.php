<?php


namespace rmatil\CmsBundle\Mapper;


use Doctrine\Common\Collections\ArrayCollection;
use rmatil\CmsBundle\Entity\Page;
use rmatil\CmsBundle\Model\PageDTO;

class PageMapper {

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

    public function pageDTOToPage(PageDTO $pageDto) : Page {
        if (null === $pageDto) {
            return null;
        }

        $page = new Page();
        $page->setId($pageDto->getId());
        $page->setUrlName($pageDto->getUrlName());

        if (null !== $pageDto->getCategory()) {
            $page->setCategory($this->pageCategoryMapper->pageCategoryDTOToPageCategory($pageDto->getCategory()));
        }

        if (null !== $pageDto->getAuthor()) {
            $page->setAuthor($this->userMapper->userDTOToUser($pageDto->getAuthor()));
        }

        if (null !== $pageDto->getLanguage()) {
            $page->setLanguage($this->languageMapper->languageDTOToLanguage($pageDto->getLanguage()));
        }

        $page->setTitle($pageDto->getTitle());

        if (null !== $pageDto->getParent()) {
            $page->setParent($this->pageDTOToPage($pageDto->getParent()));
        }

        $articles = new ArrayCollection();
        foreach ($pageDto->getArticles() as $articleDTO) {
            $article = $this->articleMapper->articleDTOToArticle($articleDTO);
            $articles->add($article);
        }
        $page->setArticles($articles);

        $page->setHasSubnavigation($pageDto->isHasSubnavigation());
        $page->setIsLockedBy($this->userMapper->userDTOToUser($pageDto->getIsLockedBy()));
        $page->setIsPublished($pageDto->isIsPublished());

        if (null !== $pageDto->getLastEditDate()) {
            $page->setLastEditDate($pageDto->getLastEditDate());
        }

        if (null !== $pageDto->getCreationDate()) {
            $page->setCreationDate($pageDto->getCreationDate());
        }

        if (null !== $pageDto->getAllowedUserGroup()) {
            $page->setAllowedUserGroup($this->userGroupMapper->userGroupDTOToUserGroup($pageDto->getAllowedUserGroup()));
        }

        $page->setIsStartPage($pageDto->isIsStartPage());

        return $page;
    }

    public function pageToPageDTO(Page $page) : PageDTO {
        if (null !== $page) {
            return null;
        }

        $pageDto = new PageDTO();
        $pageDto->setId($page->getId());
        $pageDto->setUrlName($page->getUrlName());

        if (null !== $page->getCategory()) {
            $pageDto->setCategory($this->pageCategoryMapper->pageCategoryToPageCategoryDTO($page->getCategory()));
        }

        if (null !== $page->getAuthor()) {
            $pageDto->setAuthor($this->userMapper->userToUserDTO($page->getAuthor()));
        }

        if (null !== $page->getLanguage()) {
            $pageDto->setLanguage($this->languageMapper->languageToLanguageDTO($page->getLanguage()));
        }

        $pageDto->setTitle($page->getTitle());

        if (null !== $page->getParent()) {
            $pageDto->setParent($this->pageToPageDTO($page->getParent()));
        }

        $articleDtos = new ArrayCollection();
        foreach ($page->getArticles() as $article) {
            $articleDto = $this->articleMapper->articleToArticleDTO($article);
            $articleDtos->add($articleDto);
        }
        $pageDto->setArticles($articleDtos);

        $pageDto->setHasSubnavigation($page->getHasSubnavigation());
        $pageDto->setIsLockedBy($this->userMapper->userToUserDTO($page->getIsLockedBy()));
        $pageDto->setIsPublished($page->getIsPublished());

        if (null !== $page->getLastEditDate()) {
            $pageDto->setLastEditDate($page->getLastEditDate());
        }

        if (null !== $page->getCreationDate()) {
            $pageDto->setCreationDate($page->getCreationDate());
        }

        if (null !== $page->getAllowedUserGroup()) {
            $pageDto->setAllowedUserGroup($this->userGroupMapper->userGroupToUserGroupDTO($page->getAllowedUserGroup()));
        }

        $pageDto->setIsStartPage($page->getIsStartPage());

        return $pageDto;
    }
}
