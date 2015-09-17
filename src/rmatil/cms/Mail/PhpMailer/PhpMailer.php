<?php


namespace rmatil\cms\Mail\PhpMailer;


use rmatil\cms\Mail\MailInterface;
use RuntimeException;

class PhpMailer {

    /**
     * @var \PHPMailer
     */
    protected $phpMailer;

    public function __construct($host, $username, $password, $port) {
        $this->phpMailer = new \PHPMailer(true);
        // Set mailer to use SMTP
        $this->phpMailer->isSMTP();
        $this->phpMailer->SMTPDebug = false;
        $this->phpMailer->Debugoutput = 'html';
        // update settings
        $this->phpMailer->Host = $host;
        $this->phpMailer->Username = $username;
        $this->phpMailer->Password = $password;
        $this->phpMailer->Port = intval($port);

    }

    public function send(MailInterface $mail) {
        if ( ! ($mail instanceof PhpMailerMail)) {
            throw new RuntimeException(sprintf("Mail must be of instance %s to be sent using %s", PhpMailerMail::class, PhpMailer::class));
        }

        $receiver = $mail->getTo();

        $this->phpMailer->Subject = $mail->getSubject();
        $this->phpMailer->Body = $mail->getBody();
        $this->phpMailer->From = $mail->getFromEmail();
        $this->phpMailer->FromName = $mail->getFromName();
        $this->phpMailer->WordWrap = 50;
        $this->phpMailer->addAddress($receiver['email'], $receiver['name']);
        $this->phpMailer->addReplyTo($mail->getFromEmail(), $mail->getFromName());
        $this->phpMailer->isHTML(true);

        if ( ! $this->phpMailer->send()) {
            throw new \RuntimeException($this->phpMailer->ErrorInfo);
        }
    }

}