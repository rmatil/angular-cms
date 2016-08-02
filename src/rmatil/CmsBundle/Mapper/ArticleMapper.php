<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Entity\Article;
use rmatil\CmsBundle\Model\ArticleDTO;

class ArticleMapper {

    protected $articleCategoryMapper;
    protected $languageMapper;
    protected $userGroupMapper;
    protected $userMapper;

    public function __construct(ArticleCategoryMapper $articleCategoryMapper, LanguageMapper $languageMapper, UserGroupMapper $userGroupMapper, UserMapper $userMapper) {
        $this->articleCategoryMapper = $articleCategoryMapper;
        $this->languageMapper = $languageMapper;
        $this->userGroupMapper = $userGroupMapper;
        $this->userMapper = $userMapper;
    }

    public function articleToArticleDTO(Article $article) : ArticleDTO {
        if (null === $article) {
            return null;
        }

        $articleDto = new ArticleDTO();
        $articleDto->setId($article->getId());
        $articleDto->setUrlName($article->getUrlName());

        if (null !== $article->getCategory()) {
            $articleDto->setCategory($this->articleCategoryMapper->articleCategoryToArticleCategoryDTO($article->getCategory()));
        }

        if (null !== $article->getAuthor()) {
            $articleDto->setAuthor($this->userMapper->userToUserDTO($article->getAuthor()));
        }

        if (null !== $article->getLanguage()) {
            $articleDto->setLanguage($this->languageMapper->languageToLanguageDTO($article->getLanguage()));
        }

        $articleDto->setTitle($article->getTitle());
        $articleDto->setContent($article->getContent());

        if (null !== $article->getLastEditDate()) {
            $articleDto->setLastEditDate($article->getLastEditDate());
        }

        if (null !== $article->getCreationDate()) {
            $articleDto->setCreationDate($article->getCreationDate());
        }

        $articleDto->setIsPublished($article->getIsPublished());

        if (null !== $article->getAllowedUserGroup()) {
            $articleDto->setAllowedUserGroup($this->userGroupMapper->userGroupToUserGroupDTO($article->getAllowedUserGroup()));
        }

        return $articleDto;
    }

    public function articleDTOToArticle(ArticleDTO $articleDto) : Article {
        if (null === $articleDto) {
            return null;
        }

        $article = new Article();
        $article->setId($articleDto->getId());
        $article->setUrlName($articleDto->getUrlName());

        if (null !== $articleDto->getCategory()) {
            $article->setCategory($this->articleCategoryMapper->articleCategoryDTOToArticleCategory($articleDto->getCategory()));
        }

        if (null !== $articleDto->getAuthor()) {
            $article->setAuthor($this->userMapper->userDTOToUser($articleDto->getAuthor()));
        }

        if (null !== $articleDto->getLanguage()) {
            $article->setLanguage($this->languageMapper->languageDTOToLanguage($articleDto->getLanguage()));
        }

        $article->setTitle($articleDto->getTitle());
        $article->setContent($articleDto->getContent());

        if (null !== $articleDto->getLastEditDate()) {
            $article->setLastEditDate($articleDto->getLastEditDate());
        }

        if (null !== $articleDto->getCreationDate()) {
            $article->setCreationDate($articleDto->getCreationDate());
        }

        $article->setIsPublished($articleDto->isIsPublished());


        if (null !== $articleDto->getAllowedUserGroup()) {
            $article->setAllowedUserGroup($this->userGroupMapper->userGroupDTOToUserGroup($articleDto->getAllowedUserGroup()));
        }


        return $article;
    }
}
