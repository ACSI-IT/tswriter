<?php

declare(strict_types=1);

namespace TS\Writer\Provider\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use TS\Writer\FileWriterContainer;

class WriterServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the Writer with the Silex Container.
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['writer'] = $app->share(
            function ($app) {
                $container = new FileWriterContainer($app['dispatcher']);

                $container->registerWriter('TS\\Writer\\Implementation\\Csv', 'csv');
                $container->registerWriter('TS\\Writer\\Implementation\\Ini', 'ini');
                $container->registerWriter('TS\\Writer\\Implementation\\Json', 'json');
                $container->registerWriter('TS\\Writer\\Implementation\\Txt', 'txt');
                $container->registerWriter('TS\\Writer\\Implementation\\Xml', 'xml');
                $container->registerWriter('TS\\Writer\\Implementation\\Yaml', array('yml', 'yaml'));

                return $container;
            }
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
