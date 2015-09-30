<?php


namespace rmatil\cms\DataAccessor;


use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\Location;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;

class LocationDataAccessor extends DataAccessor {

    public function __construct($em, $logger) {
        parent::__construct(EntityNames::LOCATION, $em, $logger);
    }

    public function delete($id) {
        $location = $this->em->getRepository(EntityNames::LOCATION)->find($id);

        if ( ! ($location instanceof Location)) {
            throw new EntityNotFoundException(sprintf('Could not find location with id "%s"', $id));
        }

        /** @var \rmatil\cms\Entities\Event[] $attachedEvents */
        $attachedEvents = $this->em->getRepository(EntityNames::EVENT)->findBy(array(
            'location' => $location->getId()
        ));

        foreach ($attachedEvents as $event) {
            $event->setLocation(null);
        }

        $this->em->remove($location);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->log->error($dbalex);

            throw new EntityNotDeletedException(sprintf('Could not delete location with id "%s"', $id));
        }
    }
}