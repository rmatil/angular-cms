<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Entity\UserGroup;
use rmatil\CmsBundle\Model\UserGroupDTO;

class UserGroupMapper {

    public function userGroupDTOToUserGroup(UserGroupDTO $userGroupDto) : UserGroup {
        if (null === $userGroupDto) {
            return null;
        }

        $userGroup = new UserGroup();
        $userGroup->setId($userGroupDto->getId());
        $userGroup->setName($userGroupDto->getName());
        $userGroup->setRole($userGroupDto->getRole());

        return $userGroup;
    }

    public function userGroupToUserGroupDTO(UserGroup $userGroup) : UserGroupDTO {
        if (null === $userGroup) {
            return null;
        }
        
        $userGroupDto = new UserGroupDTO();
        $userGroupDto->setId($userGroup->getId());
        $userGroupDto->setName($userGroup->getName());
        $userGroupDto->setRole($userGroup->getRole());

        return $userGroupDto;
    }
}
