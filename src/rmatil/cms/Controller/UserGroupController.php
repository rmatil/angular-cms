<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\UserGroup;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class UserGroupController extends SlimController {

    public function getUserGroupsAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::USER_GROUP)
                ->getAll()
        );
    }

    public function getUserGroupByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::USER_GROUP)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        }
    }

    public function updateUserGroupAction($userGroupId) {
        /** @var \rmatil\cms\Entities\UserGroup $userGroup */
        $userGroup = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER_GROUP, 'json');

        try {
            $userGroup = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::USER_GROUP)
                ->update($userGroup);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotUpdatedException $enue) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enue->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $userGroup);
    }

    public function insertUserGroupAction() {
        /** @var \rmatil\cms\Entities\UserGroup $userGroup */
        $userGroup = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER_GROUP, 'json');

        try {
            $userGroup = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::USER_GROUP)
                ->insert($userGroup);
        } catch (EntityInvalidException $eie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $eie->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $userGroup);
    }

    public function deleteUserGroupByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::USER_GROUP)
                ->delete($id);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotDeletedException $ende) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $ende->getMessage());
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}