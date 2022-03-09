<?php

declare(strict_types=1);

namespace TS\Writer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TS\Writer\Event\WriterEvent;

abstract class AbstractWriter implements WriterInterface
{
    /** @var array */
    protected $data = [];

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        $this->eventDispatcher->dispatch(new WriterEvent($this), WriterEvents::INIT);
    }

    /**
     * @param  EventDispatcherInterface $eventDispatcher
     * @return static
     */
    public static function factory(EventDispatcherInterface $eventDispatcher)
    {
        return new static($eventDispatcher);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param  array $data
     * @return static
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
