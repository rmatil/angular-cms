<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Entity\Language;
use rmatil\CmsBundle\Exception\MapperException;
use rmatil\CmsBundle\Model\LanguageDTO;

class LanguageMapper extends AbstractMapper {

    public function entityToDto($language) {
        if (null === $language) {
            return null;
        }

        if ( ! ($language instanceof Language)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', Language::class, get_class($language)));
        }

        $languageDto = new LanguageDTO();
        $languageDto->setId($language->getId());
        $languageDto->setName($language->getName());
        $languageDto->setCode($language->getCode());

        return $languageDto;
    }

    public function dtoToEntity($languageDto) {
        if (null === $languageDto) {
            return null;
        }

        if ( ! ($languageDto instanceof LanguageDTO)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', LanguageDTO::class, get_class($languageDto)));
        }

        $language = new Language();
        $language->setId($languageDto->getId());
        $language->setName($languageDto->getName());
        $language->setCode($languageDto->getCode());

        return $language;
    }
}
