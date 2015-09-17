<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Setting;
use rmatil\cms\Response\ResponseFactory;
use Slim\Http\Response;
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

    public function getSettingByIdAction($id) {
        $em = $this->app->entityManager;
        $setting = $em->getRepository(EntityNames::SETTING)->findOneBy(array('id' => $id));

        if ( ! ($setting instanceof Setting)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find requested setting');
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $setting);
        return;
    }

    public function postSettingsAction() {
        ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::NOT_IMPLEMENTED, 'This endpoint is not implemented');
        return;
    }

    public function updateSettingsAction($id) {
        /** @var \rmatil\cms\Entities\Setting $setting */
        $setting = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::SETTING, 'json');

        $settingsRepository = $this->app->entityManager->getRepository(EntityNames::SETTING);
        $origSetting = $settingsRepository->findOneBy(array('id' => $id));

        if ( ! ($origSetting instanceof Setting)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find setting');
            return;
        }

        $origSetting->setValue($setting->getValue());

        // force update
        try {
            $this->app->entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }
    }

    public function deleteSettingAction() {
        ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::NOT_IMPLEMENTED, 'This endpoint is not implemented');
        return;
    }
}