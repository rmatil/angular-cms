<?php

namespace rmatil\cms\Mail;

use rmatil\cms\Entities\User;

class RegistrationMail extends AMail {

    protected $user;

    protected $homepageName;


    protected $registrationLink;

    public function __construct(User $user, $homepageName, $fromAddress, $fromName, $replyToAddress, $replyToName, $registrationLink) {
        $this->user = $user;
        $this->homepageName = $homepageName;
        $this->registrationLink = $registrationLink;

        parent::__construct($this->getSubject(), $fromAddress, $fromName, array('email' => $this->user->getEmail(), 'name' => $this->getReceiverName()));
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
        $message = sprintf('Hallo %s %s', $this->user->getFirstName(), $this->user->getLastName()) . "<br /><br />";
        $message .= sprintf('Für dich wurde soeben ein neues Konto bei %s eröffnet', $this->homepageName) . "<br />";
        $message .= 'Um die Registrierung abzuschliessen, klicke bitte auf den untenstehenden Link:' . "<br /> <br />";


        $message .= sprintf('%s', $this->registrationLink) . "<br /><br />";
        $message .= sprintf('<b>Bitte beache, dass der Link aus Sicherheitsgründen nur 48h gültig ist.</b>') . "<br /><br />";


        $message .= sprintf('Bei Fragen oder Unklarheiten kontaktiere uns einfach unter %s.', $this->getFromEmail()) . "<br />";
        $message .= 'Bis bald';

        return $message;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getHomepageName() {
        return $this->homepageName;
    }

    /**
     * @param string $homepageName
     */
    public function setHomepageName($homepageName) {
        $this->homepageName = $homepageName;
    }

    /**
     * @return mixed
     */
    public function getRegistrationLink() {
        return $this->registrationLink;
    }

    /**
     * @param mixed $registrationLink
     */
    public function setRegistrationLink($registrationLink) {
        $this->registrationLink = $registrationLink;
    }

}
