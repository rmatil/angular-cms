<?php


namespace rmatil\cms\DataAccessor;


use DateTime;
use DateTimeZone;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Controller\UpdateUserGroupTrait;
use rmatil\cms\Entities\Event;
use rmatil\cms\Entities\Location;
use rmatil\cms\Entities\RepeatOption;
use rmatil\cms\Entities\User;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;

class EventDataAccessor extends DataAccessor {

    use UpdateUserGroupTrait;

    public function __construct($em, $logger) {
        parent::__construct(EntityNames::EVENT, $em, $logger);
    }

    public function update($event) {
        if ( ! ($event instanceof Event)) {
            throw new EntityInvalidException(sprintf('Required object of type "%s" but got "%s"', EntityNames::EVENT, get_class($event)));
        }

        /** @var \rmatil\cms\Entities\Event $dbEvent */
        $dbEvent = $this->em->getRepository(EntityNames::EVENT)->find($event->getId());

        if (null === $dbEvent) {
            throw new EntityNotFoundException(sprintf('Entity "%s" with id "%s" not found', $this->entityName, $event->getId()));
        }

        if ($event->getAuthor() instanceof User) {
            $event->setAuthor(
                $this->em->getRepository(EntityNames::USER)->find($event->getAuthor()->getId())
            );
        }

        if ($event->getLocation() instanceof Location) {
            $event->setLocation(
                $this->em->getRepository(EntityNames::LOCATION)->find($event->getLocation()->getId())
            );
        }

        if ($event->getRepeatOption() instanceof RepeatOption) {
            $event->setRepeatOption(
                $this->em->getRepository(EntityNames::REPEAT_OPTION)->find($event->getRepeatOption()->getId())
            );
        }

        $allUserGroups = $this->em->getRepository(EntityNames::USER_GROUP)->findAll();

        $this->updateUserGroups($allUserGroups, $event, $dbEvent);

        $dbEvent->setFile($event->getFile());
        $dbEvent->setUrlName($event->getUrlName());
        $dbEvent->setName($event->getName());
        $dbEvent->setDescription($event->getDescription());


        // we get the correct timezone in the request,
        // therefore we only have to apply the utc as timezone
        $utc = new DateTimeZone("UTC");
        if ($event->getStartDate() instanceof DateTime) {
            $event->getStartDate()->setTimezone($utc);
            $dbEvent->setStartDate($event->getStartDate());
        }

        if ($event->getEndDate() instanceof DateTime) {
            $event->getEndDate()->setTimezone($utc);
            $dbEvent->setEndDate($event->getEndDate());
        }

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->log->error($dbalex);

            throw new EntityNotUpdatedException(sprintf('Could not update entity "%s" with id "%s"', $this->entityName, $article->getId()));
        }

        return $event;
    }

    public function insert($event) {
        if ( ! ($event instanceof Event)) {
            throw new EntityInvalidException(sprintf('Required object of type "%s" but got "%s"', EntityNames::EVENT, get_class($event)));
        }

        if ($event->getAuthor() instanceof User) {
            $event->setAuthor(
                $this->em->getRepository(EntityNames::USER)->find($event->getAuthor()->getId())
            );
        }

        if ($event->getLocation() instanceof Location) {
            $event->setLocation(
                $this->em->getRepository(EntityNames::LOCATION)->find($event->getLocation()->getId())
            );
        }

        if ($event->getRepeatOption() instanceof RepeatOption) {
            $event->setRepeatOption(
                $this->em->getRepository(EntityNames::REPEAT_OPTION)->find($event->getRepeatOption()->getId())
            );
        }

        $allUserGroups = $this->em->getRepository(EntityNames::USER_GROUP)->findAll();
        $this->insertUserGroups($allUserGroups, $event);

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $event->setLastEditDate($now);
        $event->setCreationDate($now);

        $this->em->persist($event);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->log->error($dbalex);

            throw new EntityNotInsertedException(sprintf('Could not insert entity "%s"', $this->entityName));
        }

        return $event;
    }

}