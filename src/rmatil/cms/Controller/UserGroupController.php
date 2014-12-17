<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\UserGroup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use DateTime;

class UserGroupController extends SlimController {

    public function getUserGroupsAction() {
        $entityManager              = $this->app->entityManager;
        $userGroupRepository        = $entityManager->getRepository(EntityNames::USER_GROUP);
        $userGroups                 = $userGroupRepository->findAll();

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($userGroups, 'json'));
    }

    public function getUserGroupByIdAction($id) {
        $entityManager              = $this->app->entityManager;
        $userGroupRepository        = $entityManager->getRepository(EntityNames::USER_GROUP);
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
        $userGroupObj              = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER_GROUP, 'json');

        // get original page category
        $entityManager              = $this->app->entityManager;
        $userGroupRepository        = $entityManager->getRepository(EntityNames::USER_GROUP);
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
        $userGroupObj          = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER_GROUP, 'json');

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
        $userGroupRepository     = $entityManager->getRepository(EntityNames::USER_GROUP);
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