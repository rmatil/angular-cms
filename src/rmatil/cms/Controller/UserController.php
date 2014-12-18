<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\User;
use rmatil\cms\Utils\PasswordUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use DateTime;

class UserController extends SlimController {

    public function getUsersAction() {
        $entityManager   = $this->app->entityManager;
        $userRepository  = $entityManager->getRepository(EntityNames::USER);
        $users           = $userRepository->findAll();

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($users, 'json'));
    }

    public function getUserByIdAction($id) {
        $entityManager   = $this->app->entityManager;
        $userRepository  = $entityManager->getRepository(EntityNames::USER);
        $user            = $userRepository->findOneBy(array('id' => $id));

        if ($user === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        // do not show lock if requested by the same user as currently locked
        if ($user->getIsLockedBy() !== null &&
            $user->getIsLockedBy()->getId() === $_SESSION['user_id']) {
            $user->setIsLockedBy(null);
        }

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($user, 'json'));

        $userRepository             = $entityManager->getRepository(EntityNames::USER);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        // set requesting user as lock
        $user->setIsLockedBy($origUser);
        
        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }
    }

    public function updateUserAction($userId) {
        $userObject                 = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER, 'json');

        $entityManager              = $this->app->entityManager;
        $userRepository             = $entityManager->getRepository(EntityNames::USER);
        $origUser                   = $userRepository->findOneBy(array('id' => $userId));

        $userGroupRepository        = $entityManager->getRepository(EntityNames::USER_GROUP);
        $origUserGroup              = $userGroupRepository->findOneBy(array('id' => $origUser->getUserGroup()->getId()));
        $userObject->setUserGroup($origUserGroup);

        if ($userObject->getPlainPassword() === null ||
            $userObject->getPlainPassword() === '') {
            // user has not set a new password
            $userObject->setPasswordHash($origUser->getPasswordHash());
        } else {
            // hash provided plaintext password
            $userObject->setPasswordHash(PasswordUtils::hash($userObject->getPlainPassword()));
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
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($origUser, 'json'));
    }

    public function insertUserAction() {
        $userObject      = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::USER, 'json');

        $entityManager              = $this->app->entityManager;
        $userGroupRepository        = $entityManager->getRepository(EntityNames::USER_GROUP);
        $origUserGroup              = $userGroupRepository->findOneBy(array('id' => $userObject->getUserGroup()->getId()));
        $userObject->setUserGroup($origUserGroup);

        $now = new DateTime();
        $userObject->setLastLoginDate($now);
        $userObject->setRegistrationDate($now);
        $userObject->setHasEmailValidated(false);
        $userObject->setIsLocked(true);

        // sends registration email and persists the user in the db
        $this->app->registrationHandler->registerUser($userObject);

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
        $this->app->response->setBody($this->app->serializer->serialize($userObject, 'json'));
    }

    public function deleteUserByIdAction($id) {
        $entityManager   = $this->app->entityManager;
        $userRepository  = $entityManager->getRepository(EntityNames::USER);
        $user            = $userRepository->findOneBy(array('id' => $id));

        if ($user === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $registrationRepository = $entityManager->getRepository(EntityNames::REGISTRATION);
        $registration           = $registrationRepository->findOneBy(array('user' => $user));

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
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }

    public function getEmptyUserAction() {
        $user = new User();

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($user, 'json'));
    }
}