<?php

declare(strict_types=1);

namespace TS\Writer;

use ArrayAccess;
use InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TS\Writer\Exception\FactoryClassException;
use TS\Writer\Exception\FactoryTypeException;

class FileWriterContainer implements ArrayAccess
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ReflectionClass[] */
    private $registry = array();

    /** @var string[] */
    private $supportedTypes = array();

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Helper method for validateTypes().
     *
     * @param  mixed $input
     * @throws InvalidArgumentException
     */
    private function validateNotEmpty($input)
    {
        if (empty($input)) {
            throw new InvalidArgumentException('You need to supply at least one type when registering a reader.');
        }
    }

    /**
     * Validates that any given part of $types is not empty.
     *
     * @param  mixed $types
     * @return array
     */
    private function validateTypes($types)
    {
        $this->validateNotEmpty($types);

        $arrTypes = (array)$types;
        $this->validateNotEmpty($arrTypes);

        array_walk_recursive($arrTypes, array($this, 'validateNotEmpty'));

        return $arrTypes;
    }

    /**
     * Tries to create a writer for the given $type.
     *
     * @param  string $type
     * @return FileWriterInterface
     * @throws FactoryTypeException
     */
    public function createForType($type)
    {
        $writer = isset($this->supportedTypes[$type]) ? $this->supportedTypes[$type] : false;

        if ($writer) {
            return $this->registry[$writer]->newInstance($this->eventDispatcher);
        }

        throw new FactoryTypeException($type);
    }

    /**
     * Creates a writer of the given class, if previously registered.
     *
     * @param  string $writer
     * @return FileWriterInterface
     * @throws FactoryClassException
     */
    public function createInstance($writer)
    {
        if (isset($this->registry[$writer])) {
            return $this->registry[$writer]->newInstance($this->eventDispatcher);
        }

        throw new FactoryClassException($writer);
    }

    /**
     * @param  string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->supports($offset);
    }

    /**
     * @param  string $offset
     * @return FileWriterInterface
     * @throws InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        try {
            $writer = $this->createForType($offset);
        } catch (FactoryTypeException $ex) {
        }

        try {
            $writer = $this->createInstance($offset);
        } catch (FactoryClassException $ex) {
        }

        if (!isset($writer)) {
            throw new InvalidArgumentException(sprintf(
                'No writer available for offset [%s].',
                $offset
            ));
        }

        return $writer;
    }

    /**
     * @param string $offset
     * @param string $value
     */
    public function offsetSet($offset, $value)
    {
        $this->registerWriter($value, $offset);
    }

    /**
     * @param string|FileWriterInterface $offset
     */
    public function offsetUnset($offset)
    {
        if (in_array($offset, $this->supportedTypes())) {
            $offset = $this->supportedTypes[$offset];
        }

        $this->unregisterWriter($offset);
    }

    /**
     * Registers a new writer implementation with the factory.
     *
     * @param  string $writer
     * @param  array  $types
     * @return static
     * @throws InvalidArgumentException
     */
    public function registerWriter($writer, $types)
    {
        $reflection = new ReflectionClass($writer);

        if (!$reflection->implementsInterface('TS\\Writer\\FileWriterInterface')) {
            throw new InvalidArgumentException(sprintf(
                "Invalid writer type, doesn't implement [%s].",
                'TS\\Writer\\FileWriterInterface'
            ));
        }

        $types = $this->validateTypes($types);

        $this->registry[$writer] = $reflection;

        foreach ($types as $type) {
            $this->supportedTypes[$type] = $writer;
        }

        return $this;
    }

    /**
     * Returns the container supported types.
     *
     * @return array
     */
    public function supportedTypes()
    {
        return array_keys($this->supportedTypes);
    }

    /**
     * Does the container support the given type?
     *
     * @param  string $type
     * @return bool
     */
    public function supports($type)
    {
        return isset($this->registry[$type]) || in_array($type, $this->supportedTypes(), true);
    }

    /**
     * Unregisters a writer from the container.
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

            $this->supportedTypes = array_diff($this->supportedTypes, array($writer));
        }

        return $this;
    }
}
