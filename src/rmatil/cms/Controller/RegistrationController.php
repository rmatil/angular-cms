<?php

namespace rmatil\cms\Controller;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Registration;
use rmatil\cms\Exceptions\PasswordInvalidException;
use rmatil\cms\Login\PasswordHandler;
use rmatil\cms\Login\PasswordValidator;
use rmatil\cms\Response\ResponseFactory;
use rmatil\cms\Utils\PasswordUtils;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class RegistrationController extends SlimController {

    public function registerUserAction($token) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->app->entityManager;

        $registration = $em->getRepository(EntityNames::REGISTRATION)->findOneBy(array('token' => $token));

        if ( ! ($registration instanceof Registration)) {
            $user = null;
            $this->app->flashNow('registration.error', 'Token not found');
        } else {
            $now = new DateTime('now', new DateTimeZone("UTC"));
            if ($now > $registration->getExpirationDate()) {
                $this->app->flashNow('registration.error', 'Token is expired');
                $user = null;
            } else {
                $user = $registration->getUser();
            }
        }

        $this->app->render('registration-form.html.twig', array(
            'token' => $token,
            'user' => $user
        ));
    }

    public function completeRegistrationAction($token) {
        $em = $this->app->entityManager;
        $registrationRepository = $em->getRepository(EntityNames::REGISTRATION);
        $origRegistration = $registrationRepository->findOneBy(array('token' => $token));

        $submittedPass = $this->app->request->post('password');

        if ( ! ($origRegistration instanceof Registration)) {
            $this->app->flashNow('registration.error', 'Token not found');
            $this->app->render('registration-form.html.twig', array(
                'token' => $token,
                'user' => null
            ));
            return;
        }

        try {
            PasswordValidator::validatePassword($submittedPass);
        } catch (PasswordInvalidException $pie) {
            $this->app->flashNow('registration.error', 'Password must be at least 8 characters long');
            $this->app->render('registration-form.html.twig', array(
                'token' => $token,
                'user' => $origRegistration->getUser()
            ));
            return;
        }

        $passwordHash = PasswordHandler::hash($submittedPass);

        $user = $origRegistration->getUser();
        $user->setIsLocked(false);
        $user->setHasEmailValidated(true);
        $user->setPasswordHash($passwordHash);

        $em->remove($origRegistration);

        // force update
        try {
            $em->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        $this->app->redirect('/login');
    }
}