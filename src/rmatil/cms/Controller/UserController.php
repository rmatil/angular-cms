<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\User;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Exceptions\PasswordInvalidException;
use rmatil\cms\Exceptions\RegistrationMailNotSentException;
use rmatil\cms\Login\PasswordHandler;
use rmatil\cms\Login\PasswordValidator;
use rmatil\cms\Response\ResponseFactory;
use rmatil\cms\Utils\PasswordUtils;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class UserController extends SlimController {

    public function getUsersAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::USER)
                ->getAll()
        );
    }

    public function getUserByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::USER)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        }
    }

    public function updateUserAction($userId) {
        /** @var \rmatil\cms\Entities\User $user */
        $user = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER, 'json');
        $user->setId($userId);

        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::USER)
                ->update($user);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe);
            return;
        } catch (EntityNotUpdatedException $enue) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enue->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $user);
    }

    public function insertUserAction() {
        /** @var \rmatil\cms\Entities\User $user */
        $user = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER, 'json');

        try {
            $user = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::USER)
                ->insert($user);
        } catch (EntityNotInsertedException $enie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enie->getMessage());
            return;
        } catch (RegistrationMailNotSentException $rmnse) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, sprintf('Could not sent registration email: %s', $rmnse->getMessage()));
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $user);
    }

    public function deleteUserByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::USER)
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

    public function getEmptyUserAction() {
        $userGroup = $this->app->entityManager->getRepository(EntityNames::USER_GROUP)->findOneBy(array('name' => 'ROLE_USER'));
        $user = new User();
        $user->setUserGroup($userGroup);

        ResponseFactory::createJsonResponse($this->app, $user);
    }
}