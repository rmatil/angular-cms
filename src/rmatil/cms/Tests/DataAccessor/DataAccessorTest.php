<?php


namespace DataAccessor;


use DateTime;
use DateTimeZone;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\DataAccessor\DataAccessor;
use rmatil\cms\Tests\ADatabaseTestCase;

class DataAccessorTest extends ADatabaseTestCase {

    public function entityNamesDataProvider() {
        return array(
            array(EntityNames::ARTICLE_CATEGORY),
            array(EntityNames::ARTICLE_CATEGORY),
            array(EntityNames::FILE_CATEGORY),
            array(EntityNames::LANGUAGE),
            array(EntityNames::REGISTRATION),
            array(EntityNames::REPEAT_OPTION),
            array(EntityNames::SETTING),
            array(EntityNames::USER_GROUP)
        );
    }

    public function setUp() {
        parent::setUp();

        self::$testDate = new DateTime('now', new DateTimeZone('UTC'));
        self::$testChangedDate = new DateTime('yesterday', new DateTimeZone('UTC'));
    }

    /**
     * @dataProvider entityNamesDataProvider
     */
    public function testGetAll($entityName) {
        $dataAccessor = new DataAccessor($entityName, $this->em, $this->logger);
        $this->assertEmpty($dataAccessor->getAll(), sprintf('Table for entity "%s" not empty', $entityName));
    }

    /**
     * @dataProvider entityNamesDataProvider
     */
    public function testGetById($entityName) {
        $this->insertObject($entityName);

        $dataAccessor = new DataAccessor($entityName, $this->em, $this->logger);
        $obj = $dataAccessor->getById(1);

        if (method_exists($obj, 'getName')) {
            $this->assertEquals(self::$testName, $obj->getName());
        }

        if (method_exists($obj, 'getTitle')) {
            $this->assertEquals(self::$testTitle, $obj->getTitle());
        }

        if (method_exists($obj, 'getToken')) {
            $this->assertEquals(self::$testToken, $obj->getToken());
        }

        if (method_exists($obj, 'getUrlName')) {
            $this->assertEquals(self::$testUrlName, $obj->getUrlName());
        }

        if (method_exists($obj, 'getExpirationDate')) {
            $this->assertEquals(self::$testDate, $obj->getExpirationDate());
        }

        if (method_exists($obj, 'getOption')) {
            $this->assertEquals(self::$optionValue, $obj->getOption());
        }

        if (method_exists($obj, 'getLastLoginDate')) {
            $this->assertEquals(self::$testDate, $obj->getLastLoginDate());
        }

        if (method_exists($obj, 'getCode')) {
            $this->assertEquals(self::$testCode, $obj->getCode());
        }

        if (method_exists($obj, 'getValue')) {
            $this->assertEquals(self::$testValue, $obj->getValue());
        }

        if (method_exists($obj, 'getRole')) {
            $this->assertEquals(self::$testRole, $obj->getRole());
        }
    }

    /**
     * @dataProvider entityNamesDataProvider
     */
    public function testUpdate($entityName) {
        $this->insertObject($entityName);

        $ret = $this->em->getRepository($entityName)->findAll();

        $this->assignDifferentValues($ret[0]);

        $dataAccessor = new DataAccessor($entityName, $this->em, $this->logger);
        $updated = $dataAccessor->update($ret[0]);

        if (method_exists($updated, 'getName')) {
            $this->assertEquals(self::$testChangedValue, $updated->getName());
        }

        if (method_exists($updated, 'getTitle')) {
            $this->assertEquals(self::$testChangedValue, $updated->getTitle());
        }

        if (method_exists($updated, 'getToken')) {
            $this->assertEquals(self::$testChangedValue, $updated->getToken());
        }

        if (method_exists($updated, 'getUrlName')) {
            $this->assertEquals(self::$testChangedValue, $updated->getUrlName());
        }

        if (method_exists($updated, 'getExpirationDate')) {
            $this->assertEquals(self::$testChangedDate, $updated->getExpirationDate());
        }

        if (method_exists($updated, 'getOption')) {
            $this->assertEquals(self::$testChangedValue, $updated->getOption());
        }

        if (method_exists($updated, 'getLastLoginDate')) {
            $this->assertEquals(self::$testChangedDate, $updated->getLastLoginDate());
        }

        if (method_exists($updated, 'getCode')) {
            $this->assertEquals(self::$testChangedValue, $updated->getCode());
        }

        if (method_exists($updated, 'getValue')) {
            $this->assertEquals(self::$testChangedValue, $updated->getValue());
        }

        if (method_exists($updated, 'getRole')) {
            $this->assertEquals(self::$testChangedValue, $updated->getRole());
        }
    }

    /**
     * @dataProvider entityNamesDataProvider
     */
    public function testInsert($entityName) {
        $object = new $entityName();
        $this->assignValues($object);

        $dataAccessor = new DataAccessor($entityName, $this->em, $this->logger);
        $object = $dataAccessor->insert($object);

        $ret = $dataAccessor->getAll();

        $this->assertCount(1, $ret);
        $this->assertContains($object, $ret);
    }

    /**
     * @dataProvider entityNamesDataProvider
     */
    public function testDelete($entityName) {
        $inserted = $this->insertObject($entityName);

        $dataAccessor = new DataAccessor($entityName, $this->em, $this->logger);

        $ret = $dataAccessor->getById($inserted->getId());
        $this->assertEquals($inserted, $ret);

        $dataAccessor->delete($inserted->getId());

        $ret = $dataAccessor->getAll();
        $this->assertEmpty($ret);
    }

}
