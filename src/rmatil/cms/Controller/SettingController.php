<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use SlimController\SlimController;

class SettingController extends SlimController {

    public function getSettingsAction() {
        $entityManager      = $this->app->entityManager;
        $settingRepository  = $entityManager->getRepository(EntityNames::SETTING);
        $settings           = $settingRepository->findAll();

        $returnValues       = array();
        foreach ($settings as $entry) {
            $returnValues[$entry->getName()] = $entry;
        }

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($returnValues, 'json'));
    }

    public function updateSettingsAction() {
        $settings = json_decode($this->app->request->getBody(), true);

        $entityManager      = $this->app->entityManager;
        $settingsRepository = $entityManager->getRepository(EntityNames::SETTING);

        foreach ($settings as $entry) {
            $origSetting = $settingsRepository->findOneBy(array('id' => $entry['id']));

            if ($origSetting === null) {
                continue;
            }

            $origSetting->setValue($entry['value']);
        }

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }
    }
}