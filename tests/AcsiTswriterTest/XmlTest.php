<?php

declare(strict_types=1);

namespace AcsiTswriterTest;

use ReflectionObject;
use TS\Writer\Implementation\Xml;
use TS\Writer\Exception\DumpingException;

class XmlTest extends BaseTest
{
    protected $data = array(
        'array'      => array('key1' => 'value1', 'key2' => 'value2'),
        'bool'       => true,
        'float'      => 3.14,
        'int'        => 1,
        'null'       => null,
        'string'     => 'value',
        'xmlEntity'  => array(
            '@attributes' => array(
                'key1' => 'value1',
                'key2' => 'value2',
            ),
            '@cdata'      => 'A CDATA string that would possibly need escaping.',
        ),
        'xmlEntity2' => array(
            '@value' => 'test',
        ),
    );

    /**
     * @var Xml
     */
    private $writer;

    protected function setUp(): void
    {
        $this->writer = new Xml($this->dispatcher);
        $this->writer->setTargetFile($this->tmpDir . 'xmlFile.xml');
        $this->writer->setData($this->data);
    }

    protected function tearDown(): void
    {
        $this->writer = null;
        @unlink($this->tmpDir . 'xmlFile.xml');
    }

    public function testFactory(): void
    {
        $this->assertInstanceOf('TS\\Writer\\Implementation\\Xml', Xml::factory($this->dispatcher));
    }

    public function testXmlAccessors(): void
    {
        $this->writer->setEncoding('ISO-8559-1');
        $this->writer->setPrettyPrint(false);
        $this->writer->setRootNode('foo');

        $reflection = new ReflectionObject($this->writer);

        $encoding = $reflection->getProperty('encoding');
        $encoding->setAccessible(true);

        $this->assertEquals('ISO-8559-1', $encoding->getValue($this->writer));

        $prettyPrint = $reflection->getProperty('prettyPrint');
        $prettyPrint->setAccessible(true);

        $this->assertFalse($prettyPrint->getValue($this->writer));

        $rootNode = $reflection->getProperty('rootNode');
        $rootNode->setAccessible(true);

        $this->assertEquals('foo', $rootNode->getValue($this->writer));
    }

    public function testWriteAllObjectDumpingException(): void
    {
        $this->expectException(DumpingException::class);
        $this->expectExceptionMessage("Type object can't be converted to xml.");

        $this->writer->setData($this->getData());
        $this->assertTrue($this->writer->writeAll());
    }

    public function testWriteAllInvalidTagException(): void
    {
        $this->expectException(DumpingException::class);
        $this->expectExceptionMessage('tag');

        $data = array(
            0 => 'value'
        );

        $this->writer->setData($data);
        $this->assertTrue($this->writer->writeAll());
    }

    public function testWriteAllInvalidAttributeException()
    {
        $this->expectException(DumpingException::class);
        $this->expectExceptionMessage('attribute');

        $data = array(
            'xmlEntity' => array(
                '@attributes' => array(
                    0 => 'value',
                ),
            ),
        );

        $this->writer->setData($data);
        $this->assertTrue($this->writer->writeAll());
    }

    public function testWriteAll()
    {
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rootNode>
  <array>
    <key1>value1</key1>
    <key2>value2</key2>
  </array>
  <bool>true</bool>
  <float>3.14</float>
  <int>1</int>
  <null></null>
  <string>value</string>
  <xmlEntity key1="value1" key2="value2"><![CDATA[A CDATA string that would possibly need escaping.]]></xmlEntity>
  <xmlEntity2>test</xmlEntity2>
</rootNode>

XML;

        $this->assertTrue($this->writer->writeAll());

        $this->assertEquals($expected, $this->writer->dumpData());
        $this->assertEquals($expected, file_get_contents($this->tmpDir . 'xmlFile.xml'));
    }

    public function testWriteAllNumericIndizes()
    {
        $data = array(
            'subNode' => array('value1', 'value2'),
        );

        $this->writer->setData($data);
        $this->assertTrue($this->writer->writeAll());

        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rootNode>
  <subNode>value1</subNode>
  <subNode>value2</subNode>
</rootNode>

XML;

        $this->assertEquals($expected, $this->writer->dumpData());
        $this->assertEquals($expected, file_get_contents($this->tmpDir . 'xmlFile.xml'));
    }
}
