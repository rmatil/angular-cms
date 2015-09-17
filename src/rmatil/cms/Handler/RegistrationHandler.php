<?php

namespace rmatil\cms\Handler;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\Registration;
use rmatil\cms\Entities\User;
use rmatil\cms\Exceptions\RegistrationMailNotSentException;
use rmatil\cms\Exceptions\UserNotSavedException;
use rmatil\cms\Mail\Mandrill\MandrillTemplateMail;
use rmatil\cms\Mail\Mandrill\MandrillTemplateMailer;
use rmatil\cms\Mail\PhpMailer\PhpMailer;
use rmatil\cms\Mail\PhpMailer\PhpMailerMail;
use rmatil\cms\Mail\RegistrationMail;

class RegistrationHandler {

    protected $entityManager;

    protected $usedMailer;

    public function __construct(EntityManager $entityManager, $usedMailer) {
        $this->entityManager = $entityManager;
        $this->usedMailer = $usedMailer;
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

        $settingsRepo = $this->entityManager->getRepository(EntityNames::SETTING);
        $websiteName = $settingsRepo->findOneBy(array('name' => 'website_name'));
        $websiteEmail = $settingsRepo->findOneBy(array('name' => 'website_email'));
        $websiteReplyToEmail = $settingsRepo->findOneBy(array('name' => 'website_reply_to_email'));
        $websiteUrl = $settingsRepo->findOneBy(array('name' => 'website_url'));

        $registrationLink = $this->getRegistrationLink($token, $websiteUrl->getValue());

        $this->entityManager->persist($registration);

        $mail = new RegistrationMail($user,
            $websiteName->getValue(),
            $websiteEmail->getValue(),
            $websiteUrl->getValue(),
            $websiteReplyToEmail->getValue(),
            $websiteName->getValue(),
            $registrationLink
        );

        try {
            $this->sendRegistrationMail($mail);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new RegistrationMailNotSentException($e->getMessage());
        }
    }

    public function getRegistrationLink($token, $websiteUrl) {
        return sprintf(PROTOCOL . '%s/registration/%s', $websiteUrl, $token);;
    }

    public function sendRegistrationMail(RegistrationMail $mail) {
        switch ($this->usedMailer) {
            case 'mailChimp':
                $this->sendMailChimpMail($mail);
                break;
            case 'phpMailer':
                $this->sendPhpMailerMail($mail);
                break;
            default:
                throw new \RuntimeException('Could not send email. No Mailer specified');
        }
    }

    protected function sendMailChimpMail(RegistrationMail $registrationMail) {
        $config = ConfigurationHandler::readConfiguration(CONFIG_FILE);
        $mailChimpConfig = $config['mail']['mailChimp'];

        $receiver = $registrationMail->getTo();

        $mergeVars = array(
            'rcpt' => $receiver['email'],
            'vars' => array(
                array(
                    "name" => "FNAME",
                    "content" => $registrationMail->getUser()->getFirstname()
                ),
                array(
                    "name" => "REGLINK",
                    "content" => $registrationMail->getRegistrationLink()
                ),
                array(
                    "name" => 'HOMLINK',
                    "content" => $registrationMail->getFromName()
                )
            )
        );

        $mail = new MandrillTemplateMail(
            $registrationMail->getSubject(),
            $registrationMail->getFromEmail(),
            $registrationMail->getFromName(),
            $registrationMail->getTo(),
            $mergeVars,
            array('registration')
        );

        $globalMergeVars = array();
        foreach ($mailChimpConfig['globalMergeVars'] as $key => $val) {
            $globalMergeVars[] = array(
                'name' => $key,
                'content' => $val
            );
        }

        $mailChimpMailer = new MandrillTemplateMailer(
            $mailChimpConfig['apiKey'],
            $mailChimpConfig['templateName'],
            $mailChimpConfig['templateContent'],
            $globalMergeVars
        );

        try {
            $mailChimpMailer->send($mail);
        } catch (\Mandrill_Error $e) {
            throw new RegistrationMailNotSentException($e->getMessage());
        }

    }

    protected function sendPhpMailerMail(RegistrationMail $registrationMail) {
        $config = ConfigurationHandler::readConfiguration(CONFIG_FILE);
        $mailConfig = $config['mail']['phpMailer'];

        $phpMailerMail = new PhpMailerMail(
            $registrationMail->getSubject(),
            $registrationMail->getFromEmail(),
            $registrationMail->getFromName(),
            $registrationMail->getTo(),
            $registrationMail->getBody()
        );

        $phpMailer = new PhpMailer($mailConfig['host'], $mailConfig['username'], $mailConfig['password'], $mailConfig['port']);

        try {
            $phpMailer->send($phpMailerMail);
        } catch (Exception $e) {
            throw new RegistrationMailNotSentException($e->getMessage());
        }
    }


}