<?php

declare(strict_types=1);

namespace TS\Writer;

use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TS\Writer\Exception\FactoryException;

class FileWriterFactory
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var FileWriterInterface[] */
    private $registry = array();

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Tries to create a writer for the given $type.
     *
     * @param  string              $type
     * @return FileWriterInterface
     * @throws FactoryException
     */
    public function createForType($type)
    {
        foreach ($this->registry as $writer) {
            if ($writer->supports($type)) {
                return clone $writer;
            }
        }

        throw new FactoryException($type);
    }

    /**
     * Registers a new writer implementation with the factory.
     *
     * @param  string|FileWriterInterface $writer
     * @return static
     * @throws InvalidArgumentException
     */
    public function registerWriter($writer)
    {
        if (is_string($writer)) {
            $instance = new $writer($this->eventDispatcher);
            $class    = $writer;
        } elseif (is_object($writer)) {
            $instance = $writer;
            $class    = get_class($writer);
        }

        if (!isset($instance) || !($instance instanceof FileWriterInterface)) {
            throw new InvalidArgumentException(sprintf(
                "Invalid Writer type, doesn't implement [%s].",
                'TS\\Writer\\FileWriterInterface'
            ));
        }

        if (!isset($this->registry[$class])) {
            $this->registry[$class] = $instance;
        }

        return $this;
    }

    /**
     * Unregisters a writer from the factory's registry.
     *
     * @param  string|object $writer
     * @return static
     */
    public function unregisterWriter($writer)
    {
        if (is_object($writer)) {
            $writer = get_class($writer);
        }

        if (isset($this->registry[$writer])) {
            unset($this->registry[$writer]);
        }

        return $this;
    }
}
