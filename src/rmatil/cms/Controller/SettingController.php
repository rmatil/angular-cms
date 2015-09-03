<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Setting;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class SettingController extends SlimController {

    public function getSettingsAction() {
        $entityManager = $this->app->entityManager;
        $settingRepository = $entityManager->getRepository(EntityNames::SETTING);
        $settings = $settingRepository->findAll();

        $returnValues = array();
        foreach ($settings as $entry) {
            $returnValues[$entry->getName()] = $entry;
        }

        ResponseFactory::createJsonResponse($this->app, $returnValues);
    }

    public function updateSettingsAction() {
        $settings = json_decode($this->app->request->getBody(), true);

        $entityManager = $this->app->entityManager;
        $settingsRepository = $entityManager->getRepository(EntityNames::SETTING);

        foreach ($settings as $entry) {
            if ( ! is_array($entry)) {
                continue;
            }

            $origSetting = $settingsRepository->findOneBy(array('id' => $entry['id']));

            if (!($origSetting instanceof Setting)) {
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