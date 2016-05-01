<?php


namespace rmatil\cms\Mail;


use rmatil\cms\Exceptions\InvalidConfigurationException;
use rmatil\cms\Handler\ConfigurationHandler;
use rmatil\cms\Mail\Mandrill\MandrillTemplateMailer;
use rmatil\cms\Mail\PhpMailer\PhpMailer;

class MailSender {

    protected $configuredMailer;

    /**
     * @var MailerInterface
     */
    protected $mailer;

    protected $supportedMailers = array(
        PhpMailer::MAILER_NAME,
        MandrillTemplateMailer::MAILER_NAME
    );

    public function __construct($configuredMailer) {
        $this->configuredMailer = strtolower($configuredMailer);

        if (! in_array($this->configuredMailer, $this->supportedMailers)) {
            throw new InvalidConfigurationException(sprintf("No supported mailer found for %s", $this->configuredMailer));
        }

        switch ($this->configuredMailer) {
            case PhpMailer::MAILER_NAME:
                $this->mailer = new PhpMailer();
                break;
            case MandrillTemplateMailer::MAILER_NAME:
                $this->mailer = new MandrillTemplateMailer();
                break;
        }
    }

    public function send(MailInterface $mail) {
        return $this->mailer->send($mail);
    }
}
