<?php


namespace DataAccessor;


use DateTime;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\DataAccessor\PageDataAccessor;
use rmatil\cms\Entities\Location;
use rmatil\cms\Entities\Page;
use rmatil\cms\Tests\ADatabaseTestCase;

class PageDataAccessorTest extends ADatabaseTestCase {

    protected static $DATE;
    protected static $DATE_2;
    protected static $TITLE = 'name';
    protected static $TITLE_2 = 'name_2';
    protected static $URL_NAME = 'url_name';
    protected static $URL_NAME_2 = 'url_name_2';

    public function setUp() {
        parent::setUp();

        self::$DATE = new DateTime('now');
        self::$DATE_2 = new DateTime('yesterday');
    }

    public function testGetAll() {
        $dataAccessor = new PageDataAccessor($this->em, $this->logger);
        $pages = $dataAccessor->getAll();

        $this->assertEmpty($pages, 'Table page not empty');
    }

    public function testGetById() {
        $this->insertPage();

        $dataAccessor = new PageDataAccessor($this->em, $this->logger);
        $page = $dataAccessor->getById(1);

        $this->assertEquals(self::$DATE, $page->getLastEditDate());
        $this->assertEquals(self::$TITLE, $page->getTitle());
        $this->assertEquals(self::$URL_NAME, $page->getUrlName());
    }

    public function testUpdate() {
        $this->insertPage();

        $ret = $this->em->getRepository(EntityNames::PAGE)->findAll();

        $this->assignDifferentValues($ret[0]);

        $dataAccessor = new PageDataAccessor($this->em, $this->logger);
        $updated = $dataAccessor->update($ret[0]);

        $this->assertEquals(self::$DATE, $updated->getLastEditDate());
        $this->assertEquals(self::$TITLE, $updated->getTitle());
        $this->assertEquals(self::$URL_NAME, $updated->getUrlName());
    }

    public function testInsert() {
        $page = new Page();
        $this->assignValues($page);

        $dataAccessor = new PageDataAccessor($this->em, $this->logger);
        $object = $dataAccessor->insert($page);

        $ret = $dataAccessor->getAll();

        $this->assertCount(1, $ret);
        $this->assertContains($object, $ret);
    }

    public function testDelete() {
        $inserted = $this->insertPage();

        $dataAccessor = new PageDataAccessor($this->em, $this->logger);

        $ret = $dataAccessor->getById($inserted->getId());
        $this->assertEquals($inserted, $ret);

        $dataAccessor->delete($inserted->getId());

        $ret = $dataAccessor->getAll();
        $this->assertEmpty($ret);
    }

    protected function insertPage() {
        $page = new Page();

        $this->assignValues($page);

        $this->em->persist($page);
        $this->em->flush();

        return $page;
    }

    /**
     * @param $page Page
     */
    protected function assignValues($page) {
        $page->setCreationDate(self::$DATE);
        $page->setLastEditDate(self::$DATE);
        $page->setTitle(self::$TITLE);
        $page->setUrlName(self::$URL_NAME);
    }

    /**
     * @param $page Page
     */
    protected function assignDifferentValues($page) {
        $page->setCreationDate(self::$DATE_2);
        $page->setLastEditDate(self::$DATE_2);
        $page->setTitle(self::$TITLE);
        $page->setUrlName(self::$URL_NAME);
    }

}