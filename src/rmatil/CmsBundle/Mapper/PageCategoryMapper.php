<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Entity\PageCategory;
use rmatil\CmsBundle\Exception\MapperException;
use rmatil\CmsBundle\Model\PageCategoryDTO;

class PageCategoryMapper extends AbstractMapper {

    public function entityToDto($pageCategory) {
        if (null === $pageCategory) {
            return null;
        }

        if ( ! ($pageCategory instanceof PageCategory)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', PageCategory::class, get_class($pageCategory)));
        }

        $pageCategoryDTO = new PageCategoryDTO();
        $pageCategoryDTO->setId($pageCategory->getId());
        $pageCategoryDTO->setName($pageCategory->getName());

        return $pageCategoryDTO;
    }

    public function dtoToEntity($pageCategoryDto) {
        if (null === $pageCategoryDto) {
            return null;
        }

        if ( ! ($pageCategoryDto instanceof PageCategoryDTO)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', PageCategoryDTO::class, get_class($pageCategoryDto)));
        }

        $pageCategory = new PageCategory();
        $pageCategory->setId($pageCategoryDto->getId());
        $pageCategory->setName($pageCategory->getName());

        return $pageCategory;
    }
}
