<?php


namespace rmatil\cms\Mail;


use rmatil\cms\Exceptions\InvalidConfigurationException;
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

    protected $mailConfiguration = array();

    public function __construct($mailConfig) {
        if (! array_key_exists('use', $mailConfig)) {
            throw new InvalidConfigurationException('The mailer to use must be defined');
        }

        $configuredMailer = $mailConfig['use'];

        $this->configuredMailer = strtolower($configuredMailer);

        if (!in_array($this->configuredMailer, $this->supportedMailers)) {
            throw new InvalidConfigurationException(sprintf("No supported mailer found for %s", $this->configuredMailer));
        }

        switch ($this->configuredMailer) {
            case PhpMailer::MAILER_NAME:
                $this->mailer = new PhpMailer($mailConfig[PhpMailer::MAILER_NAME]);
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
