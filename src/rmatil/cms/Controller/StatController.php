<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\User;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;

class StatController extends SlimController {

    public function getStatisticsAction() {
        $entityManager      = $this->app->entityManager;

        $lastEvents         = $entityManager->createQueryBuilder()
                                ->select('e')
                                ->from(EntityNames::EVENT, 'e')
                                ->orderBy('e.startDate', 'ASC')
                                ->setMaxResults(5)
                                ->getQuery()
                                ->getResult();

        $returnValues = array();
        $returnValues['last_events'] = $lastEvents;

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($returnValues, 'json'));
    }
}