<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Entity\ArticleCategory;
use rmatil\CmsBundle\Model\ArticleCategoryDTO;

class ArticleCategoryMapper {

    public function articleCategoryToArticleCategoryDTO(ArticleCategory $articleCategory) : ArticleCategoryDTO {
        if (null === $articleCategory) {
            return null;
        }

        $articleCategoryDto = new ArticleCategoryDTO();
        $articleCategoryDto->setId($articleCategory->getId());
        $articleCategoryDto->setName($articleCategoryDto->getName());

        return $articleCategoryDto;
    }

    public function articleCategoryDTOToArticleCategory(ArticleCategoryDTO $articleCategoryDto) : ArticleCategory {
        if (null === $articleCategoryDto) {
            return null;
        }

        $articleCategory = new ArticleCategory();
        $articleCategory->setId($articleCategoryDto->getId());
        $articleCategory->setName($articleCategoryDto->getName());

        return $articleCategory;
    }
}
