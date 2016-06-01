<?php


namespace rmatil\CmsBundle\DataAccessor;


use DateTime;
use DateTimeZone;
use Doctrine\DBAL\DBALException;
use rmatil\CmsBundle\Constants\EntityNames;
use rmatil\CmsBundle\Entities\Registration;
use rmatil\CmsBundle\Entities\User;
use rmatil\CmsBundle\Entities\UserGroup;
use rmatil\CmsBundle\Exception\EntityInvalidException;
use rmatil\CmsBundle\Exception\EntityNotDeletedException;
use rmatil\CmsBundle\Exception\EntityNotFoundException;
use rmatil\CmsBundle\Exception\EntityNotInsertedException;
use rmatil\CmsBundle\Exception\EntityNotUpdatedException;
use rmatil\CmsBundle\Exception\RegistrationMailNotSentException;
use rmatil\CmsBundle\Login\PasswordHandler;

class UserDataAccessor extends DataAccessor {

    /**
     * @var \rmatil\CmsBundle\Handler\RegistrationHandler
     */
    protected $registrationHandler;

    public function __construct($registrationHandler, $em, $logger) {
        parent::__construct(EntityNames::USER, $em, $logger);

        $this->registrationHandler = $registrationHandler;
    }

    public function update($user) {
        if ( ! ($user instanceof User)) {
            throw new EntityInvalidException(sprintf('Required object of type "%s" but got "%s"', EntityNames::USER, get_class($user)));
        }

        $dbUser = $this->em->getRepository(EntityNames::USER)->find($user->getId());

        if (null === $dbUser) {
            throw new EntityNotFoundException(sprintf('Could not find user with id "%s"', $user->getId()));
        }

        if ($user->getPlainPassword() === null ||
            $user->getPlainPassword() === '') {
            // user has not set a new password
            $user->setPasswordHash($dbUser->getPasswordHash());
        } else {
            // hash provided plaintext password
            $user->setPasswordHash(PasswordHandler::hash($user->getPlainPassword()));
            $user->setPlainPassword('');
        }

        if ($user->getUserGroup() instanceof UserGroup) {
            $user->setUserGroup(
                $this->em->getRepository(EntityNames::USER_GROUP)->find($user->getUserGroup()->getId())
            );
        }

        $dbUser->update($user);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotUpdatedException($dbalex->getMessage());
        }

        return $dbUser;
    }

    public function insert($user) {
        $dbUser = $this->em->getRepository(EntityNames::USER)->findOneBy(array('userName' => $user->getUserName()));

        if (null !== $dbUser) {
            throw new EntityNotInsertedException(sprintf('User with username "%s" already exists', $user->getUserName()));
        }

        $user->setUserGroup(
            $this->em->getRepository(EntityNames::USER_GROUP)->findOneBy(array('name' => $user->getUserGroup()->getName()))
        );

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $user->setLastLoginDate($now);
        $user->setRegistrationDate($now);
        $user->setHasEmailValidated(false);
        $user->setIsLocked(true);

        // we do not set a password here, since the user
        // has to create one by himself
        $this->em->persist($user);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotInsertedException($dbalex->getMessage());
        }

        // sends registration email and persists the user in the db
        $this->registrationHandler->registerUser($user);

        return $user;
    }

    public function delete($id) {
        $dbUser = $this->em->getRepository(EntityNames::USER)->find($id);

        if (null === $dbUser) {
            throw new EntityNotFoundException(sprintf('Could not find user "%s"', $id));
        }

        $dbRegistration = $this->em->getRepository(EntityNames::REGISTRATION)->findOneBy(array('user' => $dbUser));

        if ($dbRegistration instanceof Registration) {
            $this->em->remove($dbRegistration);
        }

        $dbUser->setIsLockedBy(null);
        $dbUser->setUserGroup(null);

        $this->em->remove($dbUser);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotDeletedException($dbalex->getMessage());
        }
    }
}
