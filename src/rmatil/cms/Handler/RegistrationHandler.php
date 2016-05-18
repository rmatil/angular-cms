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
use rmatil\cms\Mail\MailSender;
use rmatil\cms\Mail\RegistrationMail\RegistrationMail;

class RegistrationHandler {

    protected $entityManager;

    protected $mailer;

    public function __construct(EntityManager $entityManager, MailSender $mailSender) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailSender;
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
            $this->mailer->send($mail);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new RegistrationMailNotSentException($e->getMessage());
        }
    }

    public function getRegistrationLink($token, $websiteUrl) {
        return sprintf('%s/registration/%s', $websiteUrl, $token);
    }

}
