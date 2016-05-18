<?php


    namespace rmatil\cms\Mail\SwiftMailer;


    use rmatil\cms\Exceptions\InvalidConfigurationException;
    use rmatil\cms\Handler\ConfigurationHandler;
    use rmatil\cms\Mail\MailerInterface;
    use rmatil\cms\Mail\MailInterface;
    use rmatil\cms\Mail\RegistrationMail\RegistrationMail;
    use rmatil\cms\Mail\RegistrationMail\SwiftMailerRegistrationMail;
    use RuntimeException;
    use Swift_Mailer;
    use Swift_Message;
    use Swift_SmtpTransport;

    class SwiftMailerMailer implements MailerInterface {

        const MAILER_NAME = 'swift_mailer';

        protected $mailer;

        public function __construct() {
            $config = ConfigurationHandler::readConfiguration(CONFIG_FILE);

            if ( ! array_key_exists('mail', $config) ||
                ! array_key_exists(SwiftMailerMailer::MAILER_NAME, $config['mail'])
            ) {
                throw new InvalidConfigurationException(sprintf('Expected a mail configuration for %s', SwiftMailerMailer::MAILER_NAME));
            }

            $swiftMailConfig = $config['mail'][SwiftMailerMailer::MAILER_NAME];

            $transport = Swift_SmtpTransport::newInstance(
                $swiftMailConfig[SwiftConstants::HOST],
                $swiftMailConfig[SwiftConstants::PORT],
                $swiftMailConfig[SwiftConstants::SECURITY]
            )
                ->setUsername($swiftMailConfig[SwiftConstants::USERNAME])
                ->setPassword($swiftMailConfig[SwiftConstants::PASSWORD]);

            $this->mailer = Swift_Mailer::newInstance($transport);
        }

        public function send(MailInterface $mail) {
            if ($mail instanceof RegistrationMail) {
                $mail = new SwiftMailerRegistrationMail($mail);
            }

            if ( ! ($mail instanceof SwiftMailerMail)) {
                throw new RuntimeException(sprintf("Mail must be of instance %s to be sent using %s", SwiftMailerMail::class, SwiftMailerMailer::class));
            }

            $receiver = $mail->getTo();

            $message = Swift_Message::newInstance()
                ->setSubject($mail->getSubject())
                ->setFrom([$mail->getFromEmail() => $mail->getFromName()])
                ->setReturnPath($mail->getFromEmail())
                ->setTo([$receiver['email'] => $receiver['name']])
                ->setBody($mail->getBody(), 'text/html')
                ->setCharset('UTF-8')
            ;

            $failedRecipients = [];

            if (0 === $this->mailer->send($message, $failedRecipients)) {
                throw new RuntimeException(sprintf('Could not send message to its recipients. Failed recipients are %s', implode(',', $failedRecipients)));
            }

            return true;
        }
    }
