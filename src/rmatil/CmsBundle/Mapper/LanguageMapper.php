<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Entity\Language;
use rmatil\CmsBundle\Model\LanguageDTO;

class LanguageMapper {

    public function languageToLanguageDTO(Language $language) : LanguageDTO {
        if (null === $language) {
            return null;
        }

        $languageDto = new LanguageDTO();
        $languageDto->setId($language->getId());
        $languageDto->setName($language->getName());
        $languageDto->setCode($language->getCode());

        return $languageDto;
    }

    public function languageDTOToLanguage(LanguageDTO $languageDto) : Language {
        if (null === $languageDto) {
            return null;
        }

        $language = new Language();
        $language->setId($languageDto->getId());
        $language->setName($languageDto->getName());
        $language->setCode($languageDto->getCode());

        return $language;
    }
}
