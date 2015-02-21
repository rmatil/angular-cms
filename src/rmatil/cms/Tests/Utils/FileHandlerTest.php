<?php

namespace rmatil\cms\Tests\Handler;

use rmatil\cms\Utils\FileUtils;
use PHPUnit_Framework_TestCase;

class FileHandlerTest extends PHPUnit_Framework_TestCase {

    private $uploadMaxFileSize;
    private $postMaxSize;

    protected function setUp() {
        $this->uploadMaxFileSize = FileUtils::getFileSizeInBytes(ini_get('upload_max_filesize'));
        $this->postMaxSize = FileUtils::getFileSizeInBytes(ini_get('post_max_size'));
    }

    public function testGetUploadMaxFileSize() {
        $size = FileUtils::getUploadMaxFileSize();

        $this->assertContains($size, array(
            $this->uploadMaxFileSize, 
            $this->postMaxSize
        ));
    }

    public function testGetFileSizeInBytes() {
        $size1 = '2K';
        $size2 = '2M';
        $size3 = '2G';
        $size4 = '2T';
        $size5 = '2P';

        $this->assertEquals(2048, FileUtils::getFileSizeInBytes($size1));
        $this->assertEquals(2097152, FileUtils::getFileSizeInBytes($size2));
        $this->assertEquals(2147483648, FileUtils::getFileSizeInBytes($size3));
        $this->assertEquals(2199023255552, FileUtils::getFileSizeInBytes($size4));
        $this->assertEquals(2251799813685248, FileUtils::getFileSizeInBytes($size5));
    }

    public function testGetFilesizeHuman() {
        $size = 512;
        $size1 = 2048;
        $size2 = 2097152;
        $size3 = 2147483648;

        $this->assertEquals('512 B', FileUtils::getFileSizeHuman($size));
        $this->assertEquals('2.0 KB', FileUtils::getFileSizeHuman($size1));
        $this->assertEquals('2.0 MB', FileUtils::getFileSizeHuman($size2));
        $this->assertEquals('2.0 GB', FileUtils::getFileSizeHuman($size3));
    }

    public function testReplaceWhitespacesFromString() {
        $string = 'Öh, wäre er doch dörthin gegängen';

        $this->assertEquals('h,-wre-er-doch-drthin-gegngen', FileUtils::replaceWhitespacesFromString($string));
    }

}