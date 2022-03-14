<?php

namespace TS\Writer\Provider\Laravel\Tests;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;
use TS\Writer\Provider\Laravel\WriterServiceProvider;

/**
 * @package   Writer
 * @author    Timo Schäfer
 * @copyright 2014
 * @version   1.2
 */
class WriterServiceProviderTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $this->application = new Application;
    }

    public function testProvides()
    {
        $provider = new WriterServiceProvider($this->application);

        $this->assertSame(array('writer', 'symfony.dispatcher'), $provider->provides());
    }

    public function testRegistration()
    {
        $this->application->register(new WriterServiceProvider($this->application));

        $this->assertInstanceOf('TS\\Writer\\FileWriterContainer', $this->application['writer']);
    }

    public function testFacade()
    {
        $this->application->register(new WriterServiceProvider($this->application));

        AliasLoader::getInstance(
            array(
                'Writer' => 'TS\\Writer\\Provider\\Laravel\\Facade\\Writer'
            )
        )->register();

        Facade::setFacadeApplication($this->application);

        $this->assertSame($this->application, \Writer::getFacadeApplication());

        $this->assertInstanceOf('TS\\Writer\\FileWriterContainer', \Writer::getFacadeRoot());
    }

    public function testBoot()
    {
        $this->application->register(new WriterServiceProvider($this->application));
        $this->application->boot();
    }

    public function testSymfonyEventDispatcherRegistered()
    {
        $this->application->register(new WriterServiceProvider($this->application));
        $this->assertInstanceOf(
            'Symfony\\Component\\EventDispatcher\\EventDispatcher',
            $this->application['symfony.dispatcher']
        );
    }

    /**
     * @dataProvider writerClasses
     */
    public function testImplementationsRegisteredAndWorking($type, $writerClass)
    {
        $this->application->register(new WriterServiceProvider($this->application));

        $writer = $this->application['writer']->createForType($type);

        $this->assertInstanceOf($writerClass, $writer);
    }

    public function writerClasses()
    {
        return array(
            array('csv', 'TS\\Writer\\Implementation\\Csv'),
            array('ini', 'TS\\Writer\\Implementation\\Ini'),
            array('json', 'TS\\Writer\\Implementation\\Json'),
            array('txt', 'TS\\Writer\\Implementation\\Txt'),
            array('xml', 'TS\\Writer\\Implementation\\Xml'),
            array('yml', 'TS\\Writer\\Implementation\\Yaml'),
        );
    }
}
