<?php

declare(strict_types=1);

namespace AcsiTswriterTest;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TS\Writer\FileWriterFactory;
use TS\Writer\Implementation\Txt;
use stdClass;
use TS\Writer\Exception\FactoryException;

class FactoryTest extends TestCase
{
    /**
     * @var FileWriterFactory
     */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new FileWriterFactory(new EventDispatcher());
    }

    protected function tearDown(): void
    {
        $this->factory = null;
    }

    public function testRegistrationException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->registerWriter(new stdClass);
    }

    public function testRegistrationWithString(): void
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

    public function testRegistrationWithInstance(): void
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

    public function testFactoryException(): void
    {
        $this->expectException(FactoryException::class);

        $this->factory->createForType('txt');
    }

    public function testFactory(): void
    {
        $instance = new Txt(new EventDispatcher());

        $this->factory->registerWriter($instance);

        $writer = $this->factory->createForType('txt');

        $this->assertEquals($instance, $writer);
    }
}
