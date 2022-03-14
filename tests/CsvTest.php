<?php

namespace TS\Writer\Tests;

use ReflectionObject;
use TS\Writer\Implementation\Csv;

/**
 * @package   Writer
 * @author    Timo Schäfer
 * @copyright 2014
 * @version   1.2
 */
class CsvTest extends BaseTest
{
    protected $data = array(
        array('value1', 'value2', 'value3'),
        array('value4', 'value5', 'value6'),
        array('value7', 'value8', 'value9'),
    );

    /**
     * @var Csv
     */
    private $writer;

    protected function setUp()
    {
        $this->writer = new Csv($this->dispatcher);
        $this->writer->setTargetFile($this->tmpDir . 'csvFile.csv');
        $this->writer->setData($this->data);
    }

    protected function tearDown(): void
    {
        $this->writer = null;
        @unlink($this->tmpDir . 'csvFile.csv');
    }

    public function testFactory()
    {
        $this->assertInstanceOf('TS\\Writer\\Implementation\\Csv', Csv::factory($this->dispatcher));
    }

    public function testCsvAccesors()
    {
        $this->writer->setDelimiter("\t");
        $this->writer->setEnclosure("'");

        $reflection = new ReflectionObject($this->writer);

        $delimiter = $reflection->getProperty('delimiter');
        $delimiter->setAccessible(true);

        $this->assertSame("\t", $delimiter->getValue($this->writer));

        $enclosure = $reflection->getProperty('enclosure');
        $enclosure->setAccessible(true);

        $this->assertSame("'", $enclosure->getValue($this->writer));
    }

    public function testWriteAll()
    {
        $expected = <<<CSV
value1,value2,value3
value4,value5,value6
value7,value8,value9

CSV;

        $this->assertTrue($this->writer->writeAll());

        $this->assertSame($expected, $this->writer->dumpData());
        $this->assertSame($expected, file_get_contents($this->tmpDir . 'csvFile.csv'));
    }
}
