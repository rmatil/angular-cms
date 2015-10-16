<?php


namespace rmatil\cms\DataAccessor;


use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;

class SettingDataAccessor extends DataAccessor {

    public function __construct($em, $logger) {
        parent::__construct(EntityNames::SETTING, $em, $logger);
    }

    public function getAll() {
        $settings = $this->em->getRepository(EntityNames::SETTING)->findAll();

        $returnValues = [];
        foreach ($settings as $setting) {
            $returnValues[$setting->getName()] = $setting;
        }

        return $returnValues;
    }

    public function update($setting) {
        $dbSetting = $this->em->getRepository(EntityNames::SETTING)->find($setting->getId());

        if (null === $dbSetting) {
            throw new EntityNotFoundException(sprintf('Could not find setting with id "%s"', $setting->getId()));
        }

        // do not allow to change setting name
        $setting->setName($dbSetting->getName());
        $dbSetting->update($setting);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->log->error($dbalex);

            throw new EntityNotUpdatedException($dbalex->getMessage());
        }

        return $dbSetting;
    }

    public function insert($setting) {
        throw new EntityInvalidException('Settings can not be inserted');
    }

    public function delete($id) {
        throw new EntityNotDeletedException('Settings can not be deleted');
    }
}