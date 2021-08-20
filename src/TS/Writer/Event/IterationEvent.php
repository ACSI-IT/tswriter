<?php

declare(strict_types=1);

namespace TS\Writer\Event;

use TS\Writer\IterableWriterInterface;

class IterationEvent extends WriterEvent
{
    /** @var IterableWriterInterface */
    protected $writer;

    /**
     * @param IterableWriterInterface $writer
     */
    public function __construct(IterableWriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @return array
     */
    public function getLastLine()
    {
        return $this->writer->getLastLine();
    }
}
