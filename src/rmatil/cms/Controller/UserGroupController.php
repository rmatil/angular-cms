<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\UserGroup;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class UserGroupController extends SlimController {

    public function getUserGroupsAction() {
        $userGroupRepository = $this->app->entityManager->getRepository(EntityNames::USER_GROUP);
        $userGroups = $userGroupRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $userGroups);
    }

    public function getUserGroupByIdAction($id) {
        $userGroupRepository = $this->app->entityManager->getRepository(EntityNames::USER_GROUP);
        $userGroup = $userGroupRepository->findOneBy(array('id' => $id));

        if (!($userGroup instanceof UserGroup)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find user group');
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $userGroup);
    }

    public function updateUserGroupAction($userGroupId) {
        /** @var \rmatil\cms\Entities\UserGroup $userGroupObj */
        $userGroupObj = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER_GROUP, 'json');

        // get original page category
        $entityManager = $this->app->entityManager;
        $userGroupRepository = $entityManager->getRepository(EntityNames::USER_GROUP);
        $origUserGroup = $userGroupRepository->findOneBy(array('id' => $userGroupId));

        if (!($origUserGroup instanceof UserGroup)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find user group');
            return;
        }

        $origUserGroup->update($userGroupObj);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $origUserGroup);
    }

    public function insertUserGroupAction() {
        $userGroupObj = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER_GROUP, 'json');

        $entityManager = $this->app->entityManager;
        $entityManager->persist($userGroupObj);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $userGroupObj);
    }

    public function deleteUserGroupByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $userGroupRepository = $entityManager->getRepository(EntityNames::USER_GROUP);
        $userGroup = $userGroupRepository->findOneBy(array('id' => $id));

        if (!($userGroup instanceof UserGroup)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find user group');
            return;
        }

        $entityManager->remove($userGroup);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}