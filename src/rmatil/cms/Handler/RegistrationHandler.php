<?php

namespace rmatil\cms\Handler;

use Doctrine\ORM\EntityManager;
use rmatil\cms\Entities\User;
use rmatil\cms\Entities\Registration;
use rmatil\cms\Exceptions\UserNotSavedException;
use rmatil\cms\Mail\MailInterface;
use rmatil\cms\Mail\RegistrationMail;
use PHPMailer;
use DateTime;
use DateInterval;

class RegistrationHandler {

    protected static $USER_FULL_QUALIFIED_CLASSNAME     = 'rmatil\cms\Entities\User';
    protected static $SETTINGS_FULL_QUALIFIED_CLASSNAME = 'rmatil\cms\Entities\Setting';

    protected $entityManager;

    protected $phpMailer;

    protected $defaultMailerSettings = array();

    public function __construct(EntityManager $entityManager, PHPMailer $phpMailer, array $mailerSettings = array()) {
        $this->entityManager    = $entityManager;
        $this->phpMailer        = $phpMailer;
        $this->initMailer($mailerSettings);
    }

    public function registerUser(User &$user) {
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('PT48H'));

        $token = hash('sha256', sprintf('%s%s%s', $user->getFirstName(), $user->getLastName(), $expirationDate->format('Y-m-d H:i:s')));

        $registration = new Registration();
        $registration->setUser($user);
        $registration->setExpirationDate($expirationDate);
        $registration->setToken($token);

        $settingsRepo        = $this->entityManager->getRepository(self::$SETTINGS_FULL_QUALIFIED_CLASSNAME);
        $websiteName         = $settingsRepo->findOneBy(array('name' => 'website_name'));
        $websiteEmail        = $settingsRepo->findOneBy(array('name' => 'website_email'));
        $websiteReplyToEmail = $settingsRepo->findOneBy(array('name' => 'website_reply_to_email'));
        $websiteUrl          = $settingsRepo->findOneBy(array('name' => 'website_url'));

        $registrationLink = sprintf('%s/registration/%s',$websiteUrl->getValue(), $registration->getToken());

        $this->entityManager->persist($user);
        $this->entityManager->persist($registration);

        try {
            $this->entityManager->flush();
        } catch (DBALException $dbalex) {
            $this->entityManager->remove($user);
            $this->entityManager->remove($registration);

            $this->entityManager->flush();

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
     * 
     * @param  array  $mailerSettings An associative array containing as 
     *                                keys the mailer property and 
     *                                as value the corresponding value
     */
    protected function initMailer(array $mailerSettings) {
        if (empty($mailerSettings)) {
            // Set PHPMailer to use the sendmail transport
            $this->phpMailer->isSendmail();
        } else {
            // Set mailer to use SMTP
            $this->phpMailer->isSMTP();                                      
            // update settings
            foreach ($mailerSettings as $key => $value) {
                if (property_exists('PHPMailer', $key)) {
                    $this->phpMailer->$key = $value;
                }
            }
        }
    }

}