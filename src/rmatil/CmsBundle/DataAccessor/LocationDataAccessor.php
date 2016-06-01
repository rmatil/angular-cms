<?php


namespace rmatil\CmsBundle\DataAccessor;


use Doctrine\DBAL\DBALException;
use rmatil\CmsBundle\Constants\EntityNames;
use rmatil\CmsBundle\Entities\Location;
use rmatil\CmsBundle\Exception\EntityNotDeletedException;
use rmatil\CmsBundle\Exception\EntityNotFoundException;

class LocationDataAccessor extends DataAccessor {

    public function __construct($em, $logger) {
        parent::__construct(EntityNames::LOCATION, $em, $logger);
    }

    public function delete($id) {
        $location = $this->em->getRepository(EntityNames::LOCATION)->find($id);

        if ( ! ($location instanceof Location)) {
            throw new EntityNotFoundException(sprintf('Could not find location with id "%s"', $id));
        }

        /** @var \rmatil\CmsBundle\Entities\Event[] $attachedEvents */
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
            $this->logger->error($dbalex);

            throw new EntityNotDeletedException(sprintf('Could not delete location with id "%s"', $id));
        }
    }
}
