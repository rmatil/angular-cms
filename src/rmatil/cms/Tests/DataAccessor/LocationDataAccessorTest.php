<?php


namespace DataAccessor;


use DateTime;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\DataAccessor\LocationDataAccessor;
use rmatil\cms\Entities\Location;
use rmatil\cms\Tests\ADatabaseTestCase;

class LocationDataAccessorTest extends ADatabaseTestCase {

    protected static $DATE;
    protected static $DATE_2;
    protected static $NAME = 'name';
    protected static $NAME_2 = 'name_2';
    protected static $DESCRIPTION = 'description';
    protected static $DESCRIPTION_2 = 'description_2';
    protected static $ADDRESS = 'address';
    protected static $ADDRESS_2 = 'address_2';
    protected static $LONGITUDE = -5.714346400000068;
    protected static $LONGITUDE_2 = -110.13013309999997;
    protected static $LATITUDE = 50.0662735;
    protected static $LATITUDE_2 = 38.1019304;

    public function setUp() {
        parent::setUp();

        self::$DATE = new DateTime('now');
        self::$DATE_2 = new DateTime('yesterday');
    }

    public function testGetAll() {
        $dataAccessor = new LocationDataAccessor($this->em, $this->logger);
        $files = $dataAccessor->getAll();

        $this->assertEmpty($files, 'Table location not empty');
    }

    public function testGetById() {
        $this->insertLocation();

        $dataAccessor = new LocationDataAccessor($this->em, $this->logger);
        $location = $dataAccessor->getById(1);

        $this->assertEquals(self::$DATE, $location->getLastEditDate());
        $this->assertEquals(self::$DESCRIPTION, $location->getDescription());
        $this->assertEquals(self::$ADDRESS, $location->getAddress());
        $this->assertEquals(self::$LATITUDE, $location->getLatitude());
        $this->assertEquals(self::$LONGITUDE, $location->getLongitude());
        $this->assertEquals(self::$NAME, $location->getName());
    }

    public function testUpdate() {
        $this->insertLocation();

        $ret = $this->em->getRepository(EntityNames::LOCATION)->findAll();

        $this->assignDifferentValues($ret[0]);

        $dataAccessor = new LocationDataAccessor($this->em, $this->logger);
        $updated = $dataAccessor->update($ret[0]);

        $this->assertEquals(self::$DATE_2, $updated->getLastEditDate());
        $this->assertEquals(self::$DESCRIPTION_2, $updated->getDescription());
        $this->assertEquals(self::$ADDRESS_2, $updated->getAddress());
        $this->assertEquals(self::$LATITUDE_2, $updated->getLatitude());
        $this->assertEquals(self::$LONGITUDE_2, $updated->getLongitude());
        $this->assertEquals(self::$NAME_2, $updated->getName());
    }

    public function testInsert() {
        $location = new Location();
        $this->assignValues($location);

        $dataAccessor = new LocationDataAccessor($this->em, $this->logger);
        $object = $dataAccessor->insert($location);

        $ret = $dataAccessor->getAll();

        $this->assertCount(1, $ret);
        $this->assertContains($object, $ret);
    }

    public function testDelete() {
        $inserted = $this->insertLocation();

        $dataAccessor = new LocationDataAccessor($this->em, $this->logger);

        $ret = $dataAccessor->getById($inserted->getId());
        $this->assertEquals($inserted, $ret);

        $dataAccessor->delete($inserted->getId());

        $ret = $dataAccessor->getAll();
        $this->assertEmpty($ret);
    }

    protected function insertLocation() {
        $location = new Location();

        $this->assignValues($location);

        $this->em->persist($location);
        $this->em->flush();

        return $location;
    }

    /**
     * @param $location Location
     */
    protected function assignValues($location) {
        $location->setCreationDate(self::$DATE);
        $location->setLastEditDate(self::$DATE);
        $location->setName(self::$NAME);
        $location->setDescription(self::$DESCRIPTION);
        $location->setAddress(self::$ADDRESS);
        $location->setLatitude(self::$LATITUDE);
        $location->setLongitude(self::$LONGITUDE);
    }

    /**
     * @param $location Location
     */
    protected function assignDifferentValues($location) {
        $location->setCreationDate(self::$DATE_2);
        $location->setLastEditDate(self::$DATE_2);
        $location->setName(self::$NAME_2);
        $location->setDescription(self::$DESCRIPTION_2);
        $location->setAddress(self::$ADDRESS_2);
        $location->setLatitude(self::$LATITUDE_2);
        $location->setLongitude(self::$LONGITUDE_2);
    }

}