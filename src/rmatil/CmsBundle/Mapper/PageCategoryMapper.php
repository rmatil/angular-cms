<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Entity\PageCategory;
use rmatil\CmsBundle\Model\PageCategoryDTO;

class PageCategoryMapper {

    public function pageCategoryDTOToPageCategory(PageCategoryDTO $pageCategoryDto) : PageCategory {
        if (null === $pageCategoryDto) {
            return null;
        }

        $pageCategory = new PageCategory();
        $pageCategory->setId($pageCategoryDto->getId());
        $pageCategory->setName($pageCategory->getName());

        return $pageCategory;
    }

    public function pageCategoryToPageCategoryDTO(PageCategory $pageCategory) : PageCategoryDTO {
        if (null === $pageCategory) {
            return null;
        }

        $pageCategoryDTO = new PageCategoryDTO();
        $pageCategoryDTO->setId($pageCategory->getId());
        $pageCategoryDTO->setName($pageCategory->getName());

        return $pageCategoryDTO;
    }
}
