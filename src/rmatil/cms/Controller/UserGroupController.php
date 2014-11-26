<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\UserGroup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use DateTime;

class UserGroupController extends SlimController {

    private static $USER_GROUP_FULL_QUALIFIED_CLASSNAME = 'rmatil\cms\Entities\UserGroup';

    public function getUserGroupsAction() {
        $entityManager              = $this->app->entityManager;
        $userGroupRepository        = $entityManager->getRepository(self::$USER_GROUP_FULL_QUALIFIED_CLASSNAME);
        $userGroups                 = $userGroupRepository->findAll();

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($userGroups, 'json'));
    }

    public function getUserGroupByIdAction($id) {
        $entityManager              = $this->app->entityManager;
        $userGroupRepository        = $entityManager->getRepository(self::$USER_GROUP_FULL_QUALIFIED_CLASSNAME);
        $userGroup                  = $userGroupRepository->findOneBy(array('id' => $id));

        if ($userGroup === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($userGroup, 'json'));
    }

    public function updateUserGroupAction($userGroupId) {
        $userGroupObj              = $this->app->serializer->deserialize($this->app->request->getBody(), self::$USER_GROUP_FULL_QUALIFIED_CLASSNAME, 'json');

        // get original page category
        $entityManager              = $this->app->entityManager;
        $userGroupRepository        = $entityManager->getRepository(self::$USER_GROUP_FULL_QUALIFIED_CLASSNAME);
        $origUserGroup              = $userGroupRepository->findOneBy(array('id' => $userGroupId));

        $origUserGroup->update($userGroupObj);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($origUserGroup, 'json'));
    }

    public function insertUserGroupAction() {
        $userGroupObj          = $this->app->serializer->deserialize($this->app->request->getBody(), self::$USER_GROUP_FULL_QUALIFIED_CLASSNAME, 'json');

        $entityManager         = $this->app->entityManager;
        $entityManager->persist($userGroupObj);

        try {
            $entityManager->flush();
        } catch(DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::CREATED);
        $this->app->response->setBody($this->app->serializer->serialize($userGroupObj, 'json'));
    }

    public function deleteUserGroupByIdAction($id) {
        $entityManager           = $this->app->entityManager;
        $userGroupRepository     = $entityManager->getRepository(self::$USER_GROUP_FULL_QUALIFIED_CLASSNAME);
        $userGroup               = $userGroupRepository->findOneBy(array('id' => $id));

        if ($userGroup === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $entityManager->remove($userGroup);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}