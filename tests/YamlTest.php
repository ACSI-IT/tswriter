<?php

namespace TS\Writer\Tests;

use ReflectionObject;
use TS\Writer\Implementation\Yaml;

/**
 * @package   Writer
 * @author    Timo Schäfer
 * @copyright 2014
 * @version   1.2
 */
class YamlTest extends BaseTest
{
    /**
     * @var Yaml
     */
    private $writer;

    protected function setUp()
    {
        $this->writer = new Yaml($this->dispatcher);
        $this->writer->setTargetFile($this->tmpDir . 'yamlFile.yml');
        $this->writer->setData($this->getData());
    }

    protected function tearDown(): void
    {
        $this->writer = null;
        @unlink($this->tmpDir . 'yamlFile.yml');
    }

    public function testFactory()
    {
        $this->assertInstanceOf('TS\\Writer\\Implementation\\Yaml', Yaml::factory($this->dispatcher));
    }

    public function testYamlAccessors()
    {
        $this->writer->setIndentation(2);
        $this->writer->setInlineLevel(2);

        $reflection = new ReflectionObject($this->writer);

        $indentation = $reflection->getProperty('indentation');
        $indentation->setAccessible(true);

        $this->assertSame(2, $indentation->getValue($this->writer));

        $inlineLevel = $reflection->getProperty('inlineLevel');
        $inlineLevel->setAccessible(true);

        $this->assertSame(2, $inlineLevel->getValue($this->writer));
    }

    public function testWriteAll()
    {
        $expected = <<<YAML
array:
    key: value
bool: true
float: 3.14
int: 1
'null': null
string: value
object: null

YAML;

        $this->assertTrue($this->writer->writeAll());

        $this->assertSame($expected, $this->writer->dumpData());
        $this->assertSame($expected, file_get_contents($this->tmpDir . 'yamlFile.yml'));
    }

    public function testWriteAllCorrectIndentationAndInline()
    {
        $this->writer->setIndentation(2);
        $this->writer->setInlineLevel(2);

        $data = array(
            'array' => array('key' => array('subkey' => 'value')),
        );

        $this->writer->setData($data);
        $this->assertTrue($this->writer->writeAll());

        $expected = <<<YAML
array:
  key: { subkey: value }

YAML;

        $this->assertSame($expected, $this->writer->dumpData());
        $this->assertSame($expected, file_get_contents($this->tmpDir . 'yamlFile.yml'));
    }
}
