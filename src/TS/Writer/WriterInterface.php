<?php

declare(strict_types=1);

namespace TS\Writer;

interface WriterInterface
{
    /**
     * Returns the previously set data array.
     *
     * @return array
     */
    public function getData();

    /**
     * Sets the data array to be written.
     *
     * @param  array $data
     * @return static
     */
    public function setData(array $data);

    /**
     * Writes all data.
     *
     * @return bool
     */
    public function writeAll();
}
