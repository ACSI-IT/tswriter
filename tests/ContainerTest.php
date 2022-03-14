<?php

namespace TS\Writer\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TS\Writer\FileWriterContainer;
use TS\Writer\Implementation\Txt;

/**
 * @package   Writer
 * @author    Timo Schäfer
 * @copyright 2014
 * @version   1.2
 */
class ContainerTest extends TestCase
{
    /**
     * @var FileWriterContainer
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new FileWriterContainer(new EventDispatcher);
    }

    protected function tearDown(): void
    {
        $this->factory = null;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegistrationInvalidTypeException()
    {
        $this->factory->registerWriter(new stdClass, 'std');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegistrationNoTypeException()
    {
        $this->factory->registerWriter('TS\\Writer\\Implementation\\Txt', null);
    }

    public function testRegistration()
    {
        $reflection = new ReflectionObject($this->factory);

        $registry = $reflection->getProperty('registry');
        $registry->setAccessible(true);

        $class = 'TS\\Writer\\Implementation\\Txt';

        $this->factory->registerWriter($class, 'txt');

        $this->assertArrayHasKey($class, $registry->getValue($this->factory));

        $this->factory->unregisterWriter($class);

        $this->assertArrayNotHasKey($class, $registry->getValue($this->factory));
    }

    public function testUnregistrationWithInstance()
    {
        $reflection = new ReflectionObject($this->factory);

        $registry = $reflection->getProperty('registry');
        $registry->setAccessible(true);

        $class = 'TS\\Writer\\Implementation\\Txt';

        $this->factory->registerWriter($class, 'txt');

        $this->assertArrayHasKey($class, $registry->getValue($this->factory));

        $instance = $this->factory->createInstance($class);

        $this->factory->unregisterWriter($instance);

        $this->assertArrayNotHasKey($class, $registry->getValue($this->factory));
    }

    /**
     * @expectedException \TS\Writer\Exception\FactoryClassException
     */
    public function testFactoryClassException()
    {
        $this->factory->createInstance('stdClass');
    }

    /**
     * @expectedException \TS\Writer\Exception\FactoryTypeException
     */
    public function testFactoryTypeException()
    {
        $this->factory->createForType('txt');
    }

    public function testFactory()
    {
        $instance = new Txt(new EventDispatcher);

        $this->factory->registerWriter('TS\\Writer\\Implementation\\Txt', 'txt');

        $writer = $this->factory->createForType('txt');

        $this->assertEquals($instance, $writer);
    }

    public function testSupports()
    {
        $this->factory->registerWriter('TS\\Writer\\Implementation\\Txt', 'txt');

        $this->assertTrue($this->factory->supports('txt'));
        $this->assertEquals(array('txt'), $this->factory->supportedTypes());
    }

    public function testArrayAccessMethods()
    {
        $reflection = new ReflectionObject($this->factory);

        $registry = $reflection->getProperty('registry');
        $registry->setAccessible(true);

        $class = 'TS\\Writer\\Implementation\\Txt';
        $type  = 'txt';

        $this->factory[$type] = $class;
        $this->assertArrayHasKey($class, $registry->getValue($this->factory));

        $writer  = $this->factory[$type];
        $writer2 = $this->factory[$class];

        $this->assertEquals($writer, $writer2);

        $this->assertTrue(isset($this->factory[$class]));
        $this->assertFalse(isset($this->factory['stdClass']));

        unset($this->factory[$class]);
        $this->assertArrayNotHasKey($class, $registry->getValue($this->factory));

        $this->factory[$type] = $class;
        unset($this->factory[$type]);
        $this->assertArrayNotHasKey($class, $registry->getValue($this->factory));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArrayAccessException()
    {
        $writer = $this->factory['asdf'];
    }
}
