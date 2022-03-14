<?php

namespace TS\Writer\Tests;

use ReflectionObject;
use TS\Writer\Exception\FilesystemException;
use TS\Writer\Implementation\Ini;

/**
 * @package   Writer
 * @author    Timo Schäfer
 * @copyright 2014
 * @version   1.2
 */
class IniTest extends BaseTest
{
    /**
     * @var Ini
     */
    private $writer;

    protected function setUp(): void
    {
        $this->writer = new Ini($this->dispatcher);
        $this->writer->setTargetFile($this->tmpDir . 'iniFile.ini');
        $this->writer->setData($this->data);
    }

    protected function tearDown(): void
    {
        $this->writer = null;
        @unlink($this->tmpDir . 'iniFile.ini');
    }

    public function testFactory()
    {
        $this->assertInstanceOf('TS\\Writer\\Implementation\\Ini', Ini::factory($this->dispatcher));
    }

    public function testDataGetter()
    {
        $this->assertSame($this->data, $this->writer->getData());
    }

    public function testFileWriterAccessors()
    {
        $this->assertSame('iniFile.ini', $this->writer->getFileName());
        $this->assertSame($this->tmpDir . 'iniFile.ini', $this->writer->getFilePath());

        $this->writer->setFileAccessMode(0);
        $this->writer->setTargetFile($this->tmpDir . 'iniFile.wrongExtension');
    }

    public function testFileWriterNotExistingPathException()
    {
        try {
            $this->writer->setTargetFile(__DIR__ . '/doesnotexist/iniFile.ini', false);
        } catch (FilesystemException $e) {
            if ($e->getMessage() == sprintf('Path [%s] does not exist.', __DIR__ . '/doesnotexist')) {
                return;
            }
        }

        $this->fail();
    }

    public function testIniAccessors()
    {
        $this->writer->setLineEnding("\r\n");

        $reflection = new ReflectionObject($this->writer);

        $lineEnding = $reflection->getProperty('lineEnding');
        $lineEnding->setAccessible(true);

        $this->assertSame("\r\n", $lineEnding->getValue($this->writer));
    }

    /**
     * @expectedException \TS\Writer\Exception\FileNotSetException
     */
    public function testFileNotSetException()
    {
        $writer = new Ini($this->dispatcher);
        $writer->writeAll();
    }

    /**
     * @expectedException \TS\Writer\Exception\DumpingException
     */
    public function testWriteAllDumpingWithObject()
    {
        $this->writer->setData($this->getData());
        $this->writer->writeAll();
    }

    /**
     * @expectedException \TS\Writer\Exception\DumpingException
     * @expectedExceptionMessage Array stack size is too deep, values can only be flat arrays.
     */
    public function testDumpingExceptionArrayTooDeep()
    {
        $this->writer->setData(array(array(array())));
        $this->writer->writeAll();
    }

    public function testWriteAll()
    {
        $this->assertTrue($this->writer->writeAll());

        $this->assertTrue(file_exists($this->tmpDir . 'iniFile.ini'));

        $expected = "array[] = \"value\"\nbool = On\nfloat = 3.14\nint = 1\nnull = \nstring = \"value\"\n";

        $this->assertEquals($expected, $this->writer->dumpData());
        $this->assertEquals($expected, file_get_contents($this->tmpDir . 'iniFile.ini'));
    }

    /**
     * @expectedException \TS\Writer\Exception\DumpingException
     * @expectedExceptionMessage Array stack size is too deep, a section can only contain another flat array.
     */
    public function testSectionedDumpingExceptionArrayTooDeep()
    {
        $this->writer->setData(array('section1' => array('key' => array('subkey' => array()))));
        $this->writer->createSections(true);

        $this->writer->writeAll();
    }

    /**
     * @expectedException \TS\Writer\Exception\DumpingException
     * @expectedExceptionMessage Sectioned ini data must have the following $data format:
     */
    public function testSectionedDumpingExceptionWrongFormat()
    {
        $this->writer->setData(array('section1' => 'meh'));
        $this->writer->createSections(true);

        $this->writer->writeAll();
    }

    /**
     * @expectedException \TS\Writer\Exception\DumpingException
     * @expectedExceptionMessage $key must be a string.
     */
    public function testSectionedDumpingExceptionNonStringKey()
    {
        $this->writer->setData(array('section1' => array(0 => 'value')));
        $this->writer->createSections(true);

        $this->writer->writeAll();
    }

    public function testSectioned()
    {
        $this->writer->setData(
            array('section1' => array('array' => array('value1', 'value2')), 'section2' => array('key' => 'value'))
        );
        $this->writer->createSections(true);

        $this->assertTrue($this->writer->writeAll());

        $expected = "[section1]\narray[] = \"value1\"\narray[] = \"value2\"\n[section2]\nkey = \"value\"\n";

        $this->assertEquals($expected, $this->writer->dumpData());
        $this->assertEquals($expected, file_get_contents($this->tmpDir . 'iniFile.ini'));
    }

    public function getEmptyFilename()
    {
        $this->writer = new Ini($this->dispatcher);

        $this->assertNull($this->writer->getFileName());
    }
}
