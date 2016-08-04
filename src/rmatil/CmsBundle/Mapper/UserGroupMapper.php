<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Entity\UserGroup;
use rmatil\CmsBundle\Exception\MapperException;
use rmatil\CmsBundle\Model\UserGroupDTO;

class UserGroupMapper extends AbstractMapper {

    public function entityToDto($userGroup) {
        if (null === $userGroup) {
            return null;
        }

        if ( ! ($userGroup instanceof UserGroup)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', UserGroup::class, get_class($userGroup)));
        }

        $userGroupDto = new UserGroupDTO();
        $userGroupDto->setId($userGroup->getId());
        $userGroupDto->setName($userGroup->getName());
        $userGroupDto->setRole($userGroup->getRole());

        return $userGroupDto;
    }

    public function dtoToEntity($userGroupDto) {
        if (null === $userGroupDto) {
            return null;
        }

        if ( ! ($userGroupDto instanceof UserGroupDTO)) {
            throw new MapperException(sprintf('Required object of type "%s" but got "%s"', UserGroupDTO::class, get_class($userGroupDto)));
        }

        $userGroup = new UserGroup();
        $userGroup->setId($userGroupDto->getId());
        $userGroup->setName($userGroupDto->getName());
        $userGroup->setRole($userGroupDto->getRole());

        return $userGroup;
    }
}
