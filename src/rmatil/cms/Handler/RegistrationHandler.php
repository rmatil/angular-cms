<?php

namespace rmatil\cms\Handler;

use DateInterval;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use PHPMailer;
use rmatil\cms\Constants\ConfigurationNames;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\Registration;
use rmatil\cms\Entities\User;
use rmatil\cms\Exceptions\UserNotSavedException;
use rmatil\cms\Mail\MailInterface;
use rmatil\cms\Mail\RegistrationMail;

class RegistrationHandler {

    protected $entityManager;

    protected $phpMailer;

    protected $defaultMailerSettings = array();

    public function __construct(EntityManager $entityManager, PHPMailer $phpMailer, array $mailerSettings = array()) {
        $this->entityManager    = $entityManager;
        $this->phpMailer        = $phpMailer;
        $this->initMailer();
    }

    /**
     * Registers the given user and starts the registration process.
     * <b>Note that any entity associated with the user object
     * must be stored beforehand in the database.</b>
     * 
     * @param User $user The user to store
     * @throws UserNotSavedException Thrown if a problem occurred during saving of the user
     */
    public function registerUser(User &$user) {
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('PT48H'));

        $token = hash('sha256', sprintf('%s%s%s', $user->getFirstName(), $user->getLastName(), $expirationDate->format('Y-m-d H:i:s')));

        $registration = new Registration();
        $registration->setUser($user);
        $registration->setExpirationDate($expirationDate);
        $registration->setToken($token);

        $settingsRepo        = $this->entityManager->getRepository(EntityNames::SETTING);
        $websiteName         = $settingsRepo->findOneBy(array('name' => 'website_name'));
        $websiteEmail        = $settingsRepo->findOneBy(array('name' => 'website_email'));
        $websiteReplyToEmail = $settingsRepo->findOneBy(array('name' => 'website_reply_to_email'));
        $websiteUrl          = $settingsRepo->findOneBy(array('name' => 'website_url'));

        $registrationLink = sprintf('%s/registration/%s', $websiteUrl->getValue(), $registration->getToken());

        $this->entityManager->persist($user->getUserGroup());
        $this->entityManager->persist($user);
        $this->entityManager->persist($registration);

        try {
            $this->entityManager->flush();
        } catch (DBALException $dbalex) {
            throw new UserNotSavedException($dbalex->getMessage());
        }

        $mail = new RegistrationMail($user, $websiteName->getValue(), 
                                     $websiteEmail->getValue(), $websiteName->getValue(), 
                                     $websiteReplyToEmail->getValue(), $websiteName->getValue(), 
                                     array('registrationLink' => $registrationLink));
        
        $this->sendRegistrationMail($mail);
    }

    public function sendRegistrationMail(MailInterface $mail) {
        $this->phpMailer->Subject   = $mail->getSubject();
        $this->phpMailer->Body      = $mail->getBody();
        $this->phpMailer->From      = $mail->getFromAddress();
        $this->phpMailer->FromName  = $mail->getFromName();
        $this->phpMailer->WordWrap  = 50;
        $this->phpMailer->addAddress($mail->getReceiverAddress(), $mail->getReceiverName());
        $this->phpMailer->addReplyTo($mail->getReplyToAddress(), $mail->getReplyToName());
        $this->phpMailer->isHTML(true);

        if (!$this->phpMailer->send()) {
            throw new \Exception($this->phpMailer->ErrorInfo);
        }
    }

    /**
     * Available mailerSettings are documented on https://github.com/PHPMailer/PHPMailer
     */
    protected function initMailer() {
        $fileHandler = HandlerSingleton::getFileHandler(HTTP_MEDIA_DIR, LOCAL_MEDIA_DIR);
        $config = $fileHandler->getConfigFileContents(CONFIG_FILE);
        $mailerSettings = $config[ConfigurationNames::MAIL_PREFIX];
        if (empty($mailerSettings)) {
            // Set PHPMailer to use the sendmail transport
            $this->phpMailer->isSendmail();
        } else {
            // Set mailer to use SMTP
            $this->phpMailer->isSMTP();
            $this->phpMailer->SMTPDebug = true;
            $this->phpMailer->Debugoutput = 'html';
            // update settings
            $this->phpMailer->Host = $mailerSettings[ConfigurationNames::MAIL_HOST];
            $this->phpMailer->Username = $mailerSettings[ConfigurationNames::MAIL_USERNAME];
            $this->phpMailer->Password = $mailerSettings[ConfigurationNames::MAIL_PASSWORD];
            $this->phpMailer->Port = intval($mailerSettings[ConfigurationNames::MAIL_PORT]);
        }
    }

}