<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\User;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;

class RegistrationController extends SlimController {

    protected static $USER_FULL_QUALIFIED_CLASSNAME     = 'rmatil\cms\Entities\User';
    protected static $REGISTRATION_FULL_QUALIFIED_CLASSNAME = 'rmatil\cms\Entities\Registration';

    public function completeRegistrationAction($token) {
        $submittedPass          = $this->app->request->post('password');

        if (strlen($submittedPass) < 8) {
            $this->app->response->setStatus(HttpStatusCodes::BAD_REQUEST);
            return;
        }

        $entityManager          = $this->app->entityManager;
        $registrationRepository = $entityManager->getRepository(self::$REGISTRATION_FULL_QUALIFIED_CLASSNAME);
        $origRegistration       = $registrationRepository->findOneBy(array('token' => $token));

        if ($origRegistration === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        // TODO: extend hashing with salt
        $passwordHash = hash('sha256', $submittedPass);

        $user = $origRegistration->getUser();
        $user->setIsLocked(false);
        $user->setHasEmailValidated(true);
        $user->setPasswordHash($passwordHash);

        $entityManager->remove($origRegistration);

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
}