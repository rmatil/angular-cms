<?php

namespace rmatil\cms\Mail;


interface MailerInterface {

    public function send(MailInterface $mail);
}