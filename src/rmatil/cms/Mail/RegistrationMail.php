<?php

namespace rmatil\cms\Mail;

use rmatil\cms\Entities\User;

class RegistrationMail implements MailInterface {

    protected $user;

    protected $homepageName;

    protected $fromAddress;

    protected $fromName;

    protected $replyToAddress;

    protected $replyToName;

    protected $params; 

    public function __construct(User $user, $homepageName, 
                                $fromAddress, $fromName,
                                $replyToAddress, $replyToName,
                                array $params) {

        $this->user             = $user;
        $this->homepageName     = $homepageName;
        $this->fromAddress      = $fromAddress;
        $this->fromName         = $fromName;
        $this->replyToAddress   = $replyToAddress;
        $this->replyToName      = $replyToName;
        $this->params           = $params;
    }

    public function getReceiverAddress() {
        return $this->user->getEmail();
    }

    public function getReceiverName() {
        return sprintf('%s %s', $this->user->getFirstName(), $this->user->getLastName());
    }

    public function getSubject() {
        return sprintf('Registrierung | %s', $this->homepageName);
    }

    public function getBody() {
        $message  = sprintf('Hallo %s %s', $this->user->getFirstName(), $this->user->getLastName())."<br /><br />";
        $message .= sprintf('Für dich wurde soeben ein neues Konto bei %s eröffnet', $this->homepageName)."<br />";
        $message .= 'Um die Registrierung abzuschliessen, klicke bitte auf den untenstehenden Link:'."<br /> <br />";

        if (array_key_exists('registrationLink', $this->params)) {
            $message .= sprintf('%s', $this->params['registrationLink'])."<br /><br />";
            $message .= sprintf('<b>Bitte beache, dass der Link aus Sicherheitsgründen nur 48h gültig ist.</b>')."<br /><br />";
        }
        
        $message .= sprintf('Bei Fragen oder Unklarheiten kontaktiere uns einfach unter %s.', $this->replyToAddress)."<br />";
        $message .= 'Bis bald';

        return $message;
    }

    public function getFromAddress() {
        return $this->fromAddress;
    }

    public function getFromName() {
        return $this->fromName;
    }

    public function getReplyToAddress() {
        return $this->replyToAddress;
    }

    public function getReplyToName() {
        return $this->replyToName;
    }
}