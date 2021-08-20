<?php

declare(strict_types=1);

namespace TS\Writer;

use Iterator;

interface IterableWriterInterface extends WriterInterface, Iterator
{
    /**
     * Gets the data array for the current line.
     *
     * @return mixed
     */
    public function getLastLine();

    /**
     * Sets the data array for the current line.
     *
     * @param mixed $lastLine
     */
    public function setLastLine($lastLine = null);

    /**
     * Writes a single line.
     *
     * @return bool
     */
    public function write();
}
