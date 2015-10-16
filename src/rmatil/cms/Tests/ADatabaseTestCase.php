<?php


namespace rmatil\cms\Tests;


use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit_Extensions_Database_DataSet_DefaultDataSet;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_TestCase;
use rmatil\cms\Utils\EntityManagerFactory;
use Slim\Log;
use Slim\LogWriter;

class ADatabaseTestCase extends PHPUnit_Extensions_Database_TestCase {

    protected static $testName = 'testName';
    protected static $testUrlName = 'urlName';
    protected static $optionValue = 'daily';
    protected static $testDate = null;
    protected static $testTitle = 'title';
    protected static $testToken = 'token';
    protected static $testCode = 'de';
    protected static $testValue = 'value';
    protected static $testRole = 'ROLE_TEST_USER';
    protected static $testChangedValue = 'testChangedValue';
    protected static $testChangedDate = null;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    protected $logger;

    public function setUp() {
        parent::setUp();

        $this->logger = new Log(new LogWriter(fopen(__DIR__ . '/../../../../app/log/test.log', 'a')));
    }

    public function getConnection() {
        $this->em = EntityManagerFactory::createEntityManager(__DIR__ . '/../../../../app/config/parameters_test.yml', __DIR__ . '/../../../../src', false);

        // Retrieve PDO instance
        $pdo = $this->em->getConnection()->getWrappedConnection();

        // Clear Doctrine to be safe
        $this->em->clear();

        // Schema Tool to process our entities
        $tool = new SchemaTool($this->em);
        $classes = $this->em->getMetaDataFactory()->getAllMetaData();

        // Drop all classes and re-build them for each test case
        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        // Pass to PHPUnit
        return $this->createDefaultDBConnection($pdo, 'angular_cms_test');
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet() {
        return new PHPUnit_Extensions_Database_DataSet_DefaultDataSet();
    }

    protected function insertObject($entityName) {
        $object = new $entityName();

        $this->assignValues($object);

        $this->em->persist($object);
        $this->em->flush();

        return $object;
    }

    protected function assignValues($object) {
        if (method_exists($object, 'setName')) {
            $object->setName(self::$testName);
        }

        if (method_exists($object, 'setTitle')) {
            $object->setTitle(self::$testTitle);
        }

        if (method_exists($object, 'setToken')) {
            $object->setToken(self::$testToken);
        }

        if (method_exists($object, 'setUrlName')) {
            $object->setUrlName(self::$testUrlName);
        }

        if (method_exists($object, 'setExpirationDate')) {
            $object->setExpirationDate(self::$testDate);
        }

        if (method_exists($object, 'setOption')) {
            $object->setOption(self::$optionValue);
        }

        if (method_exists($object, 'setLastLoginDate')) {
            $object->setLastLoginDate(self::$testDate);
        }

        if (method_exists($object, 'setCode')) {
            $object->setCode(self::$testCode);
        }

        if (method_exists($object, 'setValue')) {
            $object->setValue(self::$testValue);
        }

        if (method_exists($object, 'setRole')) {
            $object->setRole(self::$testRole);
        }
    }

    protected function assignDifferentValues($object) {
        if (method_exists($object, 'setName')) {
            $object->setName(self::$testChangedValue);
        }

        if (method_exists($object, 'setTitle')) {
            $object->setTitle(self::$testChangedValue);
        }

        if (method_exists($object, 'setToken')) {
            $object->setToken(self::$testChangedValue);
        }

        if (method_exists($object, 'setUrlName')) {
            $object->setUrlName(self::$testChangedValue);
        }

        if (method_exists($object, 'setExpirationDate')) {
            $object->setExpirationDate(self::$testChangedDate);
        }

        if (method_exists($object, 'setOption')) {
            $object->setOption(self::$testChangedValue);
        }

        if (method_exists($object, 'setLastLoginDate')) {
            $object->setLastLoginDate(self::$testChangedDate);
        }

        if (method_exists($object, 'setCode')) {
            $object->setCode(self::$testChangedValue);
        }

        if (method_exists($object, 'setValue')) {
            $object->setValue(self::$testChangedValue);
        }

        if (method_exists($object, 'setRole')) {
            $object->setRole(self::$testChangedValue);
        }
    }
}
