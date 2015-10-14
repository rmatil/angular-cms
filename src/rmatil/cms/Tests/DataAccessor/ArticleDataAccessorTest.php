<?php


namespace DataAccessor;


use rmatil\cms\Constants\EntityNames;
use rmatil\cms\DataAccessor\ArticleDataAccessor;
use rmatil\cms\Entities\Article;
use rmatil\cms\Tests\ADatabaseTestCase;

class ArticleDataAccessorTest extends ADatabaseTestCase {

    protected static $CONTENT = 'content';
    protected static $CONTENT_2 = 'content2';
    protected static $TITLE = 'title';
    protected static $TITLE_2 = 'title2';
    protected static $URL_NAME = 'urlName';
    protected static $URL_NAME_2 = 'urlName2';
    protected static $LAST_EDIT_DATE = null;
    protected static $LAST_EDIT_DATE_2 = null;
    protected static $CREATION_DATE = null;
    protected static $CREATION_DATE_2 = null;
    protected static $IS_PUBLISHED = false;
    protected static $IS_PUBLISHED_2 = true;

    public function setUp() {
        parent::setUp();

        self::$LAST_EDIT_DATE = new \DateTime('now');
        self::$LAST_EDIT_DATE_2 = new \DateTime('yesterday');

        self::$CREATION_DATE = new \DateTime('now');
        self::$CREATION_DATE_2 = new \DateTime('yesterday');
    }

    public function testGetAll() {
        $dataAccessor = new ArticleDataAccessor($this->em, $this->logger);
        $this->assertEmpty($dataAccessor->getAll(), 'Table article not empty');
    }

    public function testGetById() {
        $this->insertArticle();

        $dataAccessor = new ArticleDataAccessor($this->em, $this->logger);
        $article = $dataAccessor->getById(1);

        $this->assertEquals(self::$CONTENT, $article->getContent());
        $this->assertEquals(self::$TITLE, $article->getTitle());
        $this->assertEquals(self::$URL_NAME, $article->getUrlName());
        $this->assertEquals(self::$LAST_EDIT_DATE, $article->getLastEditDate());
        $this->assertEquals(self::$CREATION_DATE, $article->getCreationDate());
        $this->assertEquals(self::$IS_PUBLISHED, $article->getIsPublished());
    }

    public function testUpdate() {
        $this->insertArticle();

        $ret = $this->em->getRepository(EntityNames::ARTICLE)->findAll();

        $this->assignDifferentValues($ret[0]);

        $dataAccessor = new ArticleDataAccessor($this->em, $this->logger);
        $updated = $dataAccessor->update($ret[0]);

        $this->assertEquals(self::$CONTENT_2, $updated->getContent());
        $this->assertEquals(self::$TITLE_2, $updated->getTitle());
        $this->assertEquals(self::$URL_NAME_2, $updated->getUrlName());
        $this->assertEquals(self::$LAST_EDIT_DATE_2, $updated->getLastEditDate(), 'last edit date not equal');
        $this->assertEquals(self::$CREATION_DATE_2, $updated->getCreationDate(), 'creation date not equal');
        $this->assertEquals(self::$IS_PUBLISHED_2, $updated->getIsPublished());
    }

    public function testInsert() {
        $article = new Article();
        $this->assignValues($article);

        $dataAccessor = new ArticleDataAccessor($this->em, $this->logger);
        $object = $dataAccessor->insert($article);

        $ret = $dataAccessor->getAll();

        $this->assertCount(1, $ret);
        $this->assertContains($object, $ret);
    }

    public function testDelete() {
        $inserted = $this->insertArticle();

        $dataAccessor = new ArticleDataAccessor($this->em, $this->logger);

        $ret = $dataAccessor->getById($inserted->getId());
        $this->assertEquals($inserted, $ret);

        $dataAccessor->delete($inserted->getId());

        $ret = $dataAccessor->getAll();
        $this->assertEmpty($ret);
    }

    protected function insertArticle() {
        $article = new Article();

        $this->assignValues($article);

        $this->em->persist($article);
        $this->em->flush();

        return $article;
    }

    protected function assignValues($article) {
        $article->setContent(self::$CONTENT);
        $article->setTitle(self::$TITLE);
        $article->setUrlName(self::$URL_NAME);
        $article->setLastEditDate(self::$LAST_EDIT_DATE);
        $article->setCreationDate(self::$CREATION_DATE);
        $article->setIsPublished(self::$IS_PUBLISHED);
    }

    protected function assignDifferentValues($article) {
        $article->setContent(self::$CONTENT_2);
        $article->setTitle(self::$TITLE_2);
        $article->setUrlName(self::$URL_NAME_2);
        $article->setLastEditDate(self::$LAST_EDIT_DATE_2);
        $article->setCreationDate(self::$CREATION_DATE_2);
        $article->setIsPublished(self::$IS_PUBLISHED_2);
    }

}