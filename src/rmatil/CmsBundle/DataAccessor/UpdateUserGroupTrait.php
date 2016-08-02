<?php


namespace rmatil\CmsBundle\DataAccessor;


use rmatil\CmsBundle\Constants\EntityNames;
use RuntimeException;

trait UpdateUserGroupTrait {

    protected function updateUserGroups($allUserGroups, $object, $dbObject) {
        $modifiers = $this->getModifierMethods($object);

        foreach ($allUserGroups as $userGroup) {

            if ($userGroup->{$modifiers['getter']}()->contains($dbObject) &&
                ! $dbObject->getAllowedUserGroups()->contains($userGroup)
            ) {

                // maintain inverse side
                $dbObject->addAllowedUserGroup($userGroup);
            } else if ( ! $userGroup->{$modifiers['getter']}()->contains($dbObject) &&
                $dbObject->getAllowedUserGroups()->contains($userGroup)
            ) {

                // maintain inverse side
                $dbObject->removeAllowedUserGroup($userGroup);
            }

            if ( ! $userGroup->{$modifiers['getter']}()->contains($dbObject) &&
                ! $object->getAllowedUserGroups()->contains($userGroup)
            ) {

                // use this loop here, as contains() does not
                // consider a proxy object as a equally object. Basically, it isn't...
                foreach ($object->getAllowedUserGroups() as $userGroupObj) {
                    if ($userGroupObj->getId() === $userGroup->getId()) {
                        // usergroup was selected and we can add the object to the accessible usergroups
                        // and the usergroup as allowedUserGroup to the object (inside addAccessibleArticle-Method)
                        $userGroup->{$modifiers['adder']}($dbObject);
                        break;
                    }
                }
            } else if ($userGroup->{$modifiers['getter']}()->contains($dbObject) &&
                $dbObject->getAllowedUserGroups()->contains($userGroup) &&
                ! $object->getAllowedUserGroups()->contains($userGroup)
            ) {

                $doesContainObj = false;
                foreach ($object->getAllowedUserGroups() as $userGroupObj) {
                    if ($userGroupObj->getId() === $userGroup->getId()) {
                        $doesContainObj = true;
                        break;
                    }
                }

                if ( ! $doesContainObj) {
                    // usegroup was unselected and we can remove the object from the accessible usergroups
                    // and the usergroup as the allowedUserGroup from the object (inside removeAccessibleArticle)
                    $userGroup->{$modifiers['remover']}($dbObject);
                }
            }
        }
    }

    protected function insertUserGroups($allUserGroups, $object) {
        $modifiers = $this->getModifierMethods($object);

        $userGroups = $object->getAllowedUserGroups()->toArray();
        $object->getAllowedUserGroups()->clear();

        foreach ($allUserGroups as $dbUserGroup) {
            foreach ($userGroups as $userGroup) {
                if ($userGroup->getId() === $dbUserGroup->getId()) {
                    // usergroup was selected and we can add the article to the accessible usergroups
                    // and the usergroup as allowedUserGroup to the article (inside addAccessibleArticle-Method)
                    $dbUserGroup->{$modifiers['adder']}($object);
                    break;
                }
            }
        }
    }

    private function getModifierMethods($object) {
        $getter = 'getAccessible';
        $adder = 'addAccessible';
        $remover = 'removeAccessible';

        switch (get_class($object)) {
            case EntityNames::ARTICLE:
                $getter .= 'Articles';
                $adder .= 'Article';
                $remover .= 'Article';
                break;
            case EntityNames::PAGE:
                $getter .= 'Pages';
                $adder .= 'Page';
                $remover .= 'Page';
                break;
            case EntityNames::EVENT:
                $getter .= 'Events';
                $adder .= 'Event';
                $remover .= 'Event';
                break;
            default:
                throw new RuntimeException(sprintf('No accessor registered for entity "%s"', get_class($object)));
        }

        return array(
            'getter' => $getter,
            'adder' => $adder,
            'remover' => $remover
        );
    }
}
