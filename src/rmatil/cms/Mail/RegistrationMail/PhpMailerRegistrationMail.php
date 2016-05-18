<?php


namespace rmatil\cms\Mail\RegistrationMail;


use rmatil\cms\Mail\PhpMailer\PhpMailerMail;

class PhpMailerRegistrationMail extends PhpMailerMail {

    public function __construct(RegistrationMail $registrationMail) {
        parent::__construct(
            $registrationMail->getSubject(),
            $registrationMail->getFromEmail(),
            $registrationMail->getFromName(),
            $registrationMail->getTo(),
            $registrationMail->getBody()
        );
    }
}
