<?php


namespace rmatil\cms\Mail\RegistrationMail;


use rmatil\cms\Mail\Mandrill\MandrillConstants;
use rmatil\cms\Mail\Mandrill\MandrillTemplateMail;
use rmatil\cms\Mail\RegistrationMail;

class MandrillRegistrationMail extends MandrillTemplateMail {

    public function __construct(RegistrationMail $registrationMail) {
        $receiver = $registrationMail->getTo();

        $mergeVars = array(
            'rcpt' => $receiver['email'],
            'vars' => array(
                array(
                    "name" => MandrillConstants::FNAME,
                    "content" => $registrationMail->getUser()->getFirstname()
                ),
                array(
                    "name" => MandrillConstants::REGLINK,
                    "content" => $registrationMail->getRegistrationLink()
                ),
                array(
                    "name" => MandrillConstants::HOMLINK,
                    "content" => $registrationMail->getFromName()
                )
            )
        );

        parent::__construct(
            $registrationMail->getSubject(),
            $registrationMail->getFromEmail(),
            $registrationMail->getFromName(),
            $registrationMail->getTo(),
            $mergeVars,
            array('registration')
        );
    }
}
