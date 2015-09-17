<?php


namespace rmatil\cms\Mail;


abstract class AMail implements MailInterface {

    /**
     * @var string The subject
     */
    protected $subject;

    /**
     * @var string The from email address
     */
    protected $fromEmail;

    /**
     * @var string The sender name
     */
    protected $fromName;

    /**
     * @var array An array containing the keys email, name
     */
    protected $to;

    /**
     * AMail constructor.
     * @param string $subject
     * @param string $fromEmail
     * @param string $fromName
     * @param array $to
     */
    public function __construct($subject, $fromEmail, $fromName, array $to) {
        $this->subject = $subject;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getFromEmail() {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     */
    public function setFromEmail($fromEmail) {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @return string
     */
    public function getFromName() {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     */
    public function setFromName($fromName) {
        $this->fromName = $fromName;
    }

    /**
     * @return array
     */
    public function getTo() {
        return $this->to;
    }

    /**
     * @param array $to
     */
    public function setTo($to) {
        $this->to = $to;
    }


}