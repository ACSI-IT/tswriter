<?php

declare(strict_types=1);

namespace AcsiTswriterTest;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TS\Writer\Event\IterationEvent;
use TS\Writer\IterableWriter;
use TS\Writer\WriterEvents;

class ArrayWriter extends IterableWriter
{
    private $array = [];

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->array;
    }

    public function write()
    {
        $success = $this->valid();
        $data    = $this->morphData();

        if ($success && $data !== null) {
            $this->eventDispatcher->dispatch(new IterationEvent($this), WriterEvents::WRITE);

            $this->array[] = $data;
        }

        return $success;
    }
}

class IterableWriterTest extends TestCase
{
    /**
     * @var ArrayWriter
     */
    private $writer;

    protected function setUp(): void
    {
        $this->writer = new ArrayWriter(new EventDispatcher);
    }

    protected function tearDown(): void
    {
        $this->writer = null;
    }

    public function testWhatever()
    {
        $data = array(1, 2, 3, 4, 5);

        $this->writer->setData($data);

        $this->writer->writeAll();

        $this->assertEquals($data, $this->writer->getArray());
    }
}
