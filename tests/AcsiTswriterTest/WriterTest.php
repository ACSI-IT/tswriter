<?php

declare(strict_types=1);

namespace AcsiTswriterTest\Tests;

use Symfony\Component\EventDispatcher\EventDispatcher;
use TS\Writer\Implementation\Txt;

class WriterTest extends BaseTest
{
    /**
     * @var Txt
     */
    private $writer;

    protected function setUp(): void
    {
        $this->writer = new Txt(new EventDispatcher);
    }

    protected function tearDown(): void
    {
        $this->writer = null;
        @unlink($this->tmpDir . '1/2/3/test.txt');
        @unlink($this->tmpDir . '2/test.txt');
        @rmdir($this->tmpDir . '1/2/3');
        @rmdir($this->tmpDir . '1/2');
        @rmdir($this->tmpDir . '1');
        @rmdir($this->tmpDir . '2');
    }

    public function testRecursiveDirectoryCreated()
    {
        $this->writer->setTargetFile($this->tmpDir . '1/2/3/test.txt', true);
        $this->writer->setData(array('Testing recursive directory function'));
        $this->writer->writeAll();

        $this->assertTrue(is_dir($this->tmpDir . '1/2/3/'));
        $this->assertTrue(file_exists($this->tmpDir . '1/2/3/test.txt'));
    }

    public function testNoneRecursiveDirectoryCreated()
    {
        $this->writer->setTargetFile($this->tmpDir . '2/test.txt', true);
        $this->writer->setData(array('Testing recursive directory function'));
        $this->writer->writeAll();

        $this->assertTrue(is_dir($this->tmpDir . '2/'));
        $this->assertTrue(file_exists($this->tmpDir . '2/test.txt'));
    }
}
