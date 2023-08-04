<?php

declare(strict_types=1);

namespace AcsiTswriterTest;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TS\Writer\Exception\FactoryTypeException;
use TS\Writer\FileWriterContainer;
use TS\Writer\Implementation\Txt;
use TS\Writer\Exception\FactoryClassException;

class ContainerTest extends TestCase
{
    /**
     * @var FileWriterContainer
     */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new FileWriterContainer(new EventDispatcher);
    }

    protected function tearDown(): void
    {
        $this->factory = null;
    }

    public function testRegistrationInvalidTypeException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->registerWriter(new stdClass, 'std');
    }

    public function testRegistrationNoTypeException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->registerWriter('TS\\Writer\\Implementation\\Txt', null);
    }

    public function testRegistration(): void
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

    public function testUnregistrationWithInstance(): void
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

    public function testFactoryClassException(): void
    {
        $this->expectException(FactoryClassException::class);

        $this->factory->createInstance('stdClass');
    }

    public function testFactoryTypeException(): void
    {
        $this->expectException(FactoryTypeException::class);

        $this->factory->createForType('txt');
    }

    public function testFactory(): void
    {
        $instance = new Txt(new EventDispatcher);

        $this->factory->registerWriter('TS\\Writer\\Implementation\\Txt', 'txt');

        $writer = $this->factory->createForType('txt');

        $this->assertEquals($instance, $writer);
    }

    public function testSupports(): void
    {
        $this->factory->registerWriter('TS\\Writer\\Implementation\\Txt', 'txt');

        $this->assertTrue($this->factory->supports('txt'));
        $this->assertEquals(array('txt'), $this->factory->supportedTypes());
    }

    public function testArrayAccessMethods(): void
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

    public function testArrayAccessException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $writer = $this->factory['asdf'];
    }
}
