<?php

declare(strict_types=1);

namespace AcsiTswriterTest;

use ReflectionObject;
use TS\Writer\Implementation\Txt;
use TS\Writer\Exception\FileNotSetException;

class TxtTest extends BaseTest
{
    protected $data = array(
        'This is line 1 of our test data.',
        'And here\'s the second line.',
    );

    /**
     * @var Txt
     */
    private $writer;

    protected function setUp(): void
    {
        $this->writer = new Txt($this->dispatcher);
        $this->writer->setTargetFile($this->tmpDir . 'textFile.txt');
        $this->writer->setData($this->data);
    }

    protected function tearDown(): void
    {
        $this->writer = null;
        @unlink($this->tmpDir . 'textFile.txt');
    }

    public function testFactory()
    {
        $this->assertInstanceOf('TS\\Writer\\Implementation\\Txt', Txt::factory($this->dispatcher));
    }

    public function testTxtAccessors()
    {
        $this->writer->setLineEnding("\r\n");

        $reflection = new ReflectionObject($this->writer);

        $lineEnding = $reflection->getProperty('lineEnding');
        $lineEnding->setAccessible(true);

        $this->assertSame("\r\n", $lineEnding->getValue($this->writer));
    }

    public function testWriteFileNotSetException()
    {
        $this->expectException(FileNotSetException::class);

        Txt::factory($this->dispatcher)->write();
    }

    public function testWriteAllFileNotSetException()
    {
        $this->expectException(FileNotSetException::class);

        Txt::factory($this->dispatcher)->writeAll();
    }

    public function testWriteAll()
    {
        $expected = <<<TXT
This is line 1 of our test data.
And here's the second line.

TXT;

        $this->assertTrue($this->writer->writeAll());

        $this->assertSame($expected, $this->writer->dumpData());
        $this->assertSame($expected, file_get_contents($this->tmpDir . 'textFile.txt'));
    }
}
