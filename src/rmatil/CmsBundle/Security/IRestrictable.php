<?php


namespace rmatil\CmsBundle\Security;


use rmatil\CmsBundle\Entity\UserGroup;

interface IRestrictable {

    /**
     * @return UserGroup
     */
    function getAllowedUserGroup();

    /**
     * @param \rmatil\CmsBundle\Entity\UserGroup $allowedUserGroup
     */
    function setAllowedUserGroup(UserGroup $allowedUserGroup);

}
