<?php


namespace rmatil\cms\Mail\PhpMailer;


use rmatil\cms\Mail\AMail;

class PhpMailerMail extends AMail {

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
