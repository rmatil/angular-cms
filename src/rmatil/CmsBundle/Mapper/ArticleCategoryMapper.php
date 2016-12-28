<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Entity\ArticleCategory;
use rmatil\CmsBundle\Exception\MapperException;
use rmatil\CmsBundle\Model\ArticleCategoryDTO;

class ArticleCategoryMapper extends AbstractMapper {

    public function entityToDto($articleCategory) {
        if (null === $articleCategory) {
            return null;
        }

        if ( ! ($articleCategory instanceof ArticleCategory)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', ArticleCategory::class, get_class($articleCategory)));
        }

        $articleCategoryDto = new ArticleCategoryDTO();
        $articleCategoryDto->setId($articleCategory->getId());
        $articleCategoryDto->setName($articleCategory->getName());

        return $articleCategoryDto;
    }

    public function dtoToEntity($articleCategoryDto) {
        if (null === $articleCategoryDto) {
            return null;
        }

        if ( ! ($articleCategoryDto instanceof ArticleCategoryDTO)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', ArticleCategoryDTO::class, get_class($articleCategoryDto)));
        }

        $articleCategory = new ArticleCategory();
        $articleCategory->setId($articleCategoryDto->getId());
        $articleCategory->setName($articleCategoryDto->getName());

        return $articleCategory;
    }
}
