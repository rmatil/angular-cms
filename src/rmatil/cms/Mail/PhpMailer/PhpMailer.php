<?php


namespace rmatil\cms\Mail\PhpMailer;


use rmatil\cms\Exceptions\InvalidConfigurationException;
use rmatil\cms\Mail\MailerInterface;
use rmatil\cms\Mail\MailInterface;
use rmatil\cms\Mail\RegistrationMail\PhpMailerRegistrationMail;
use rmatil\cms\Mail\RegistrationMail\RegistrationMail;
use RuntimeException;

class PhpMailer implements MailerInterface {

    const MAILER_NAME = 'php_mailer';

    protected $phpMailerConfig;

    /**
     * @var \PHPMailer
     */
    protected $phpMailer;

    public function __construct(array $phpMailerConfig) {
        $requiredParameters = array(
            PhpMailerConstants::CHAR_SET,
            PhpMailerConstants::SMTP_AUTH,
            PhpMailerConstants::HOST,
            PhpMailerConstants::USERNAME,
            PhpMailerConstants::PASSWORD,
            PhpMailerConstants::PORT
        );

        // check whether all keys are in the array
        if (0 !== count(array_diff($requiredParameters, array_keys($phpMailerConfig)))) {
            throw new InvalidConfigurationException(
                sprintf(
                    'Missing parameters for %s. Expected %s but only found %s',
                    PhpMailer::MAILER_NAME,
                    implode(',', $requiredParameters),
                    implode(',', $phpMailerConfig)
                )
            );
        }

        $this->phpMailer = new \PHPMailer(true);
        // Set mailer to use SMTP
        $this->phpMailer->isSMTP();
        $this->phpMailer->SMTPDebug = false;
        $this->phpMailer->Debugoutput = 'html';
        // update settings
        $this->phpMailer->Host = $phpMailerConfig[PhpMailerConstants::HOST];
        $this->phpMailer->Username = $phpMailerConfig[PhpMailerConstants::USERNAME];
        $this->phpMailer->Password = $phpMailerConfig[PhpMailerConstants::PASSWORD];
        $this->phpMailer->Port = intval($phpMailerConfig[PhpMailerConstants::PORT]);

    }

    public function send(MailInterface $mail) {
        if (!($mail instanceof PhpMailerMail)) {
            throw new RuntimeException(sprintf("Mail must be of instance %s to be sent using %s", PhpMailerMail::class, PhpMailer::class));
        }

        if ($mail instanceof RegistrationMail) {
            $mail = new PhpMailerRegistrationMail($mail);
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
