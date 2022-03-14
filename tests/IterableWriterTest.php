<?php

namespace TS\Writer\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TS\Writer\Event\IterationEvent;
use TS\Writer\IterableWriter;
use TS\Writer\WriterEvents;

/**
 * @package   Writer
 * @author    Timo Schäfer
 * @copyright 2014
 * @version   1.2
 */
class ArrayWriter extends IterableWriter
{
    private $array = array();

    public function getArray()
    {
        return $this->array;
    }

    public function write()
    {
        $success = $this->valid();
        $data    = $this->morphData();

        if ($success && $data !== null) {
            $this->eventDispatcher->dispatch(WriterEvents::WRITE, new IterationEvent($this));

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

    protected function setUp()
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
