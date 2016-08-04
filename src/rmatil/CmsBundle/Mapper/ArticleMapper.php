<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Constants\EntityNames;
use rmatil\CmsBundle\Entity\Article;
use rmatil\CmsBundle\Exception\MapperException;
use rmatil\CmsBundle\Model\ArticleDTO;

class ArticleMapper extends AbstractMapper {

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

    public function entityToDto($article) {
        if (null === $article) {
            return null;
        }

        if ( ! ($article instanceof Article)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', EntityNames::ARTICLE, get_class($article)));
        }

        $articleDto = new ArticleDTO();
        $articleDto->setId($article->getId());
        $articleDto->setUrlName($article->getUrlName());

        if (null !== $article->getCategory()) {
            $articleDto->setCategory($this->articleCategoryMapper->entityToDto($article->getCategory()));
        }

        if (null !== $article->getAuthor()) {
            $articleDto->setAuthor($this->userMapper->entityToDto($article->getAuthor()));
        }

        if (null !== $article->getLanguage()) {
            $articleDto->setLanguage($this->languageMapper->entityToDto($article->getLanguage()));
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
            $articleDto->setAllowedUserGroup($this->userGroupMapper->entityToDto($article->getAllowedUserGroup()));
        }

        return $articleDto;
    }

    public function dtoToEntity($articleDto) {
        if (null === $articleDto) {
            return null;
        }

        if ( ! ($articleDto instanceof ArticleDTO)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', ArticleDTO::class, get_class($articleDto)));
        }

        $article = new Article();
        $article->setId($articleDto->getId());
        $article->setUrlName($articleDto->getUrlName());

        if (null !== $articleDto->getCategory()) {
            $article->setCategory($this->articleCategoryMapper->dtoToEntity($articleDto->getCategory()));
        }

        if (null !== $articleDto->getAuthor()) {
            $article->setAuthor($this->userMapper->dtoToEntity($articleDto->getAuthor()));
        }

        if (null !== $articleDto->getLanguage()) {
            $article->setLanguage($this->languageMapper->dtoToEntity($articleDto->getLanguage()));
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
            $article->setAllowedUserGroup($this->userGroupMapper->dtoToEntity($articleDto->getAllowedUserGroup()));
        }


        return $article;
    }
}
