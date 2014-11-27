<?php

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\User;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use DateTime;

class RegistrationController extends SlimController {

    public function updateUserAction() {
        // if ($userObject->getPlainPassword() === null ||
        //     $userObject->getPlainPassword() === '') {
        //     $now = new DateTime();
        //     $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), 'Ein Passwort muss angegeben werden'));
        //     $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
        //     return;
        // } else {
        //     // hash provided plaintext password
        //     if (function_exists('password_hash')) {
        //         $hash = password_hash($userObject->getPlainPassword(), PASSWORD_DEFAULT);    
        //     } else {
        //         $hash = hash('sha512', sprintf("%s", $userObject->getPlainPassword()));
        //     }
            
        //     $userObject->setPasswordHash($hash);
        // }
    }
}