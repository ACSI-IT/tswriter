<?php

namespace TS\Writer\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TS\Writer\FileWriterFactory;
use TS\Writer\Implementation\Txt;
use stdClass;

/**
 * @package   Writer
 * @author    Timo Schäfer
 * @copyright 2013
 * @version   1.0
 */
class FactoryTest extends TestCase
{
    /**
     * @var FileWriterFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new FileWriterFactory(new EventDispatcher());
    }

    protected function tearDown(): void
    {
        $this->factory = null;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegistrationException()
    {
        $this->factory->registerWriter(new stdClass);
    }

    public function testRegistrationWithString()
    {
        $reflection = new ReflectionObject($this->factory);

        $registry = $reflection->getProperty('registry');
        $registry->setAccessible(true);

        $class = 'TS\\Writer\\Implementation\\Txt';

        $this->factory->registerWriter($class);

        $this->assertArrayHasKey($class, $registry->getValue($this->factory));

        $this->factory->unregisterWriter($class);

        $this->assertArrayNotHasKey($class, $registry->getValue($this->factory));
    }

    public function testRegistrationWithInstance()
    {
        $reflection = new ReflectionObject($this->factory);

        $registry = $reflection->getProperty('registry');
        $registry->setAccessible(true);

        $class    = 'TS\\Writer\\Implementation\\Txt';
        $instance = new Txt(new EventDispatcher());

        $this->factory->registerWriter($instance);

        $this->assertArrayHasKey($class, $registry->getValue($this->factory));

        $this->factory->unregisterWriter($instance);

        $this->assertArrayNotHasKey($class, $registry->getValue($this->factory));
    }

    /**
     * @expectedException \TS\Writer\Exception\FactoryException
     */
    public function testFactoryException()
    {
        $this->factory->createForType('txt');
    }

    public function testFactory()
    {
        $instance = new Txt(new EventDispatcher());

        $this->factory->registerWriter($instance);

        $writer = $this->factory->createForType('txt');

        $this->assertEquals($instance, $writer);
    }
}
