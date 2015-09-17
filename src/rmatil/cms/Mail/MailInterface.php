<?php


namespace rmatil\cms\Mail;


interface MailInterface {

    public function getSubject();

    public function getFromEmail();

    public function getFromName();

    public function getTo();

}