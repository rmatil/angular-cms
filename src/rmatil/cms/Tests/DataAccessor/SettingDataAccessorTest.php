<?php


namespace DataAccessor;


use rmatil\cms\Constants\EntityNames;
use rmatil\cms\DataAccessor\SettingDataAccessor;
use rmatil\cms\Entities\Setting;
use rmatil\cms\Tests\ADatabaseTestCase;

class SettingDataAccessorTest extends ADatabaseTestCase {

    protected static $NAME = 'name';
    protected static $NAME_2 = 'name2';
    protected static $VALUE = 'value';
    protected static $VALUE_2 = 'value2';

    public function setUp() {
        parent::setUp();
    }

    public function testGetAll() {
        $dataAccessor = new SettingDataAccessor($this->em, $this->logger);
        $settings = $dataAccessor->getAll();

        $this->assertEmpty($settings, 'Table setting not empty');
    }

    public function testGetById() {
        $this->insertSetting();

        $dataAccessor = new SettingDataAccessor($this->em, $this->logger);
        $setting = $dataAccessor->getById(1);

        $this->assertEquals(self::$NAME, $setting->getName());
        $this->assertEquals(self::$VALUE, $setting->getValue());
    }

    public function testUpdate() {
        $this->insertSetting();

        $ret = $this->em->getRepository(EntityNames::SETTING)->findAll();

        $this->assignDifferentValues($ret[0]);

        $dataAccessor = new SettingDataAccessor($this->em, $this->logger);
        $updated = $dataAccessor->update($ret[0]);

        $this->assertEquals(self::$NAME_2, $updated->getName());
        $this->assertEquals(self::$VALUE_2, $updated->getValue());
    }

    /**
     * @expectedException \rmatil\cms\Exceptions\EntityInvalidException
     */
    public function testInsert() {
        $setting = new Setting();
        $this->assignValues($setting);

        $dataAccessor = new SettingDataAccessor($this->em, $this->logger);
        $object = $dataAccessor->insert($setting);

        $ret = $dataAccessor->getAll();

        $this->assertCount(1, $ret);
        $this->assertContains($object, $ret);
    }

    /**
     * @expectedException \rmatil\cms\Exceptions\EntityNotDeletedException
     */
    public function testDelete() {
        $inserted = $this->insertSetting();

        $dataAccessor = new SettingDataAccessor($this->em, $this->logger);

        $ret = $dataAccessor->getById($inserted->getId());
        $this->assertEquals($inserted, $ret);

        $dataAccessor->delete($inserted->getId());

        $ret = $dataAccessor->getAll();
        $this->assertEmpty($ret);
    }

    protected function insertSetting() {
        $setting = new Setting();

        $this->assignValues($setting);

        $this->em->persist($setting);
        $this->em->flush();

        return $setting;
    }

    /**
     * @param $setting Setting
     */
    protected function assignValues($setting) {
        $setting->setName(self::$NAME);
        $setting->setValue(self::$VALUE);
    }

    /**
     * @param $setting Setting
     */
    protected function assignDifferentValues($setting) {
        $setting->setName(self::$NAME_2);
        $setting->setValue(self::$VALUE_2);
    }
}