<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\User;
use rmatil\cms\Exceptions\RegistrationMailNotSentException;
use rmatil\cms\Login\PasswordHandler;
use rmatil\cms\Response\ResponseFactory;
use rmatil\cms\Utils\PasswordUtils;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class UserController extends SlimController {

    public function getUsersAction() {
        $userRepository = $this->app->entityManager->getRepository(EntityNames::USER);
        $users = $userRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $users);
    }

    public function getUserByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $user = $userRepository->findOneBy(array('id' => $id));

        if ( ! ($user instanceof User)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find user');
            return;
        }

        // do not show lock if requested by the same user as currently locked
        if (($user->getIsLockedBy() instanceof User) &&
            $user->getIsLockedBy()->getId() === $_SESSION['user_id']
        ) {
            $user->setIsLockedBy(null);
        }

        ResponseFactory::createJsonResponse($this->app, $user);

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        // set requesting user as lock
        $user->setIsLockedBy($origUser);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }
    }

    public function updateUserAction($userId) {
        /** @var \rmatil\cms\Entities\User $userObject */
        $userObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER, 'json');

        $entityManager = $this->app->entityManager;
        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $userId));

        if ( ! ($origUser instanceof User)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find user');
            return;
        }

        $userGroupRepository = $entityManager->getRepository(EntityNames::USER_GROUP);
        $origUserGroup = $userGroupRepository->findOneBy(array('id' => $origUser->getUserGroup()->getId()));
        $userObject->setUserGroup($origUserGroup);

        if ($userObject->getPlainPassword() === null ||
            $userObject->getPlainPassword() === ''
        ) {
            // user has not set a new password
            $userObject->setPasswordHash($origUser->getPasswordHash());
        } else {
            // hash provided plaintext password
            $userObject->setPasswordHash(PasswordHandler::hash($userObject->getPlainPassword()));
        }

        $origUser->update($userObject);
        // release lock on editing
        $origUser->setIsLockedBy(null);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $origUser);
    }

    public function insertUserAction() {
        /** @var \rmatil\cms\Entities\User $userObject */
        $userObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER, 'json');

        $entityManager = $this->app->entityManager;
        $userGroupRepository = $entityManager->getRepository(EntityNames::USER_GROUP);
        $origUserGroup = $userGroupRepository->findOneBy(array('id' => $userObject->getUserGroup()->getId()));
        $userObject->setUserGroup($origUserGroup);

        $now = new DateTime();
        $userObject->setLastLoginDate($now);
        $userObject->setRegistrationDate($now);
        $userObject->setHasEmailValidated(false);
        $userObject->setIsLocked(true);

        $entityManager->persist($userObject);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        try {
            // sends registration email and persists the user in the db
            $this->app->registrationHandler->registerUser($userObject);
        } catch (RegistrationMailNotSentException $rmnse) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, sprintf('Could not sent registration email: %s', $rmnse->getMessage()));
            return;
        }


        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $userObject);
    }

    public function deleteUserByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $user = $userRepository->findOneBy(array('id' => $id));

        if ( ! ($user instanceof User)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find user');
            return;
        }

        $registrationRepository = $entityManager->getRepository(EntityNames::REGISTRATION);
        $registration = $registrationRepository->findOneBy(array('user' => $user));

        if ($registration !== null) {
            $entityManager->remove($registration);
        }

        // prevent conflict on foreign key constraint
        $user->setIsLockedBy(null);
        $user->setUserGroup(null);

        $entityManager->remove($user);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }

    public function getEmptyUserAction() {
        $user = new User();

        ResponseFactory::createJsonResponse($this->app, $user);
    }
}