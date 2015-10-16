<?php


namespace DataAccessor;


use DateTime;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\DataAccessor\EventDataAccessor;
use rmatil\cms\Entities\Event;
use rmatil\cms\Tests\ADatabaseTestCase;

class EventDataAccessorTest extends ADatabaseTestCase {

    protected static $DATE;
    protected static $DATE_2;
    protected static $URL_NAME = 'url_name';
    protected static $URL_NAME_2 = 'url_name_2';
    protected static $NAME = 'name';
    protected static $NAME_2 = 'name_2';
    protected static $DESCRIPTION = 'description';
    protected static $DESCRIPTION_2 = 'description_2';

    public function setUp() {
        parent::setUp();

        self::$DATE = new DateTime('now');
        self::$DATE_2 = new DateTime('yesterday');
    }

    public function testGetAll() {
        $dataAccessor = new EventDataAccessor($this->em, $this->logger);
        $events = $dataAccessor->getAll();

        $this->assertEmpty($events, 'Table events not empty');
    }

    public function testGetById() {
        $this->insertEvent();

        $dataAccessor = new EventDataAccessor($this->em, $this->logger);
        $event = $dataAccessor->getById(1);

        $this->assertEquals(self::$DATE, $event->getStartDate());
        $this->assertEquals(self::$DATE, $event->getEndDate());
        $this->assertEquals(self::$DATE, $event->getCreationDate());
        $this->assertEquals(self::$DATE, $event->getLastEditDate());
        $this->assertEquals(self::$URL_NAME, $event->getUrlName());
        $this->assertEquals(self::$NAME, $event->getName());
        $this->assertEquals(self::$DESCRIPTION, $event->getDescription());
    }

    public function testUpdate() {
        $this->insertEvent();

        $ret = $this->em->getRepository(EntityNames::EVENT)->findAll();

        $this->assignDifferentValues($ret[0]);

        $dataAccessor = new EventDataAccessor($this->em, $this->logger);
        $updated = $dataAccessor->update($ret[0]);

        $this->assertEquals(self::$DATE_2, $updated->getStartDate());
        $this->assertEquals(self::$DATE_2, $updated->getEndDate());
        $this->assertEquals(self::$DATE_2, $updated->getCreationDate());
        $this->assertEquals(self::$DATE_2, $updated->getLastEditDate());
        $this->assertEquals(self::$URL_NAME_2, $updated->getUrlName());
        $this->assertEquals(self::$NAME_2, $updated->getName());
        $this->assertEquals(self::$DESCRIPTION_2, $updated->getDescription());
    }

    public function testInsert() {
        $event = new Event();
        $this->assignValues($event);

        $dataAccessor = new EventDataAccessor($this->em, $this->logger);
        $object = $dataAccessor->insert($event);

        $ret = $dataAccessor->getAll();

        $this->assertCount(1, $ret);
        $this->assertContains($object, $ret);
    }

    public function testDelete() {
        $inserted = $this->insertEvent();

        $dataAccessor = new EventDataAccessor($this->em, $this->logger);

        $ret = $dataAccessor->getById($inserted->getId());
        $this->assertEquals($inserted, $ret);

        $dataAccessor->delete($inserted->getId());

        $ret = $dataAccessor->getAll();
        $this->assertEmpty($ret);
    }

    protected function insertEvent() {
        $event = new Event();

        $this->assignValues($event);

        $this->em->persist($event);
        $this->em->flush();

        return $event;
    }

    /**
     * @param $event Event
     */
    protected function assignValues($event) {
        $event->setCreationDate(self::$DATE);
        $event->setLastEditDate(self::$DATE);
        $event->setUrlName(self::$URL_NAME);
        $event->setDescription(self::$DESCRIPTION);
        $event->setEndDate(self::$DATE);
        $event->setName(self::$NAME);
        $event->setStartDate(self::$DATE);
    }

    /**
     * @param $event Event
     */
    protected function assignDifferentValues($event) {
        $event->setCreationDate(self::$DATE_2);
        $event->setLastEditDate(self::$DATE_2);
        $event->setUrlName(self::$URL_NAME_2);
        $event->setDescription(self::$DESCRIPTION_2);
        $event->setEndDate(self::$DATE_2);
        $event->setName(self::$NAME_2);
        $event->setStartDate(self::$DATE_2);
    }

}