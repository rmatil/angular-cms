<?php


namespace rmatil\cms\Mail\SwiftMailer;


use rmatil\cms\Mail\AMail;

class SwiftMailerMail extends AMail {

    protected $body;

    public function __construct($subject, $fromEmail, $fromName, array $to, $body) {
        $this->body = $body;
        parent::__construct($subject, $fromEmail, $fromName, $to);
    }

    /**
     * @return mixed
     */
    public function getBody() {
        return $this->body;
    }
}
