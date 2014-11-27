<?php

namespace rmatil\cms\Mail;

use rmatil\cms\Entities\User;

interface MailInterface {

    public function __construct(User $user, $homepageName, 
                                $fromAddress, $fromName,
                                $replyToAddress, $replyToName,
                                array $params);

    public function getReceiverAddress();

    public function getReceiverName();

    public function getSubject();

    public function getBody();

    public function getFromAddress();

    public function getFromName();

    public function getReplyToAddress();

    public function getReplyToName();
}