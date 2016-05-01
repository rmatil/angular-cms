<?php


namespace rmatil\cms\Mail\PhpMailer;


use rmatil\cms\Exceptions\InvalidConfigurationException;
use rmatil\cms\Handler\ConfigurationHandler;
use rmatil\cms\Mail\MailInterface;
use RuntimeException;

class PhpMailer {

    const MAILER_NAME = 'php_mailer';

    /**
     * @var \PHPMailer
     */
    protected $phpMailer;

    public function __construct() {
        $config = ConfigurationHandler::readConfiguration(CONFIG_FILE);

        if (! array_key_exists('mail', $config) ||
            ! array_key_exists(PhpMailer::MAILER_NAME, $config['mail'])) {
            throw new InvalidConfigurationException(sprintf('Expected a mail configuration for %s', PhpMailer::MAILER_NAME));
        }

        $mailConfig = $config['mail'][PhpMailer::MAILER_NAME];

        $this->phpMailer = new \PHPMailer(true);
        // Set mailer to use SMTP
        $this->phpMailer->isSMTP();
        $this->phpMailer->SMTPDebug = false;
        $this->phpMailer->Debugoutput = 'html';
        // update settings
        $this->phpMailer->Host = $mailConfig['host'];
        $this->phpMailer->Username = $mailConfig['username'];
        $this->phpMailer->Password = $mailConfig['password'];
        $this->phpMailer->Port = intval($mailConfig['port']);

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

        return true;
    }

}
