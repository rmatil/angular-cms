<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Entity\User;
use rmatil\CmsBundle\Model\UserDTO;

class UserMapper {

    public function userToUserDTO(User $user) : UserDTO {
        if (null === $user) {
            return null;
        }

        $userDto = new UserDTO();
        $userDto->setId($user->getId());
        $userDto->setFirstName($user->getFirstName());
        $userDto->setLastName($user->getLastName());
        $userDto->setPhoneNumber($user->getPhoneNumber());
        $userDto->setMobileNumber($user->getMobileNumber());
        $userDto->setAddress($user->getAddress());
        $userDto->setZipCode($user->getZipCode());
        $userDto->setPlace($user->getPlace());
        $userDto->setEmail($user->getEmail());
        $userDto->setIsLocked($user->isLocked());
        $userDto->setIsExpired($user->isExpired());
        $userDto->setIsExpired($user->isEnabled());

        if (null !== $user->getLastLogin()) {
            $userDto->setLastLoginDate($user->getLastLogin());
        }

        $userDto->setRoles($user->getRoles());

        return $userDto;
    }

    public function userDTOToUser(UserDTO $userDto) : User {
        if (null === $userDto) {
            return null;
        }

        $user = new User();
        $user->setId($userDto->getId());
        $user->setFirstName($userDto->getFirstName());
        $user->setLastName($userDto->getLastName());
        $user->setPhoneNumber($userDto->getPhoneNumber());
        $user->setMobileNumber($userDto->getMobileNumber());
        $user->setAddress($userDto->getAddress());
        $user->setZipCode($userDto->getZipCode());
        $user->setPlace($userDto->getPlace());
        $user->setEmail($userDto->getEmail());
        $user->setLocked($userDto->isLocked());
        $user->setExpired($userDto->isExpired());
        $user->setExpired($userDto->isEnabled());

        if (null !== $userDto->getLastLoginDate()) {
            $user->setLastLogin($userDto->getLastLoginDate());
        }

        $user->setRoles($userDto->getRoles());

        return $user;
    }
}
