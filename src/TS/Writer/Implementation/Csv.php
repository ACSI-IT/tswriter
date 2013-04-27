<?php

namespace TS\Writer\Implementation;

use TS\Writer\IterableFileWriter;

/**
 * Csv
 *
 * @package   Writer
 * @author    Timo Schäfer
 * @copyright 2013
 * @version   1.0
 */
class Csv extends IterableFileWriter
{
    /**
     * @var string
     */
    private $delimiter = ',';

    /**
     * @var string
     */
    private $enclosure = '"';

    /**
     * @var resource
     */
    private $handle;

    /**
     * Returns and creates the resource handle, if necessary.
     *
     * @return resource
     */
    private function getHandle()
    {
        if (!is_resource($this->handle)) {
            $this->handle = fopen($this->file, 'w+');
        }

        return $this->handle;
    }

    /**
     * Closes the handle on object destruction.
     */
    public function __destruct()
    {
        $this->closeHandle();
    }

    /**
     * Actual line writing logic.
     *
     * @param  mixed $data
     * @return bool
     */
    protected function writeLine($data)
    {
        $handle = $this->getHandle();

        flock($handle, \LOCK_EX);
        $success = (bool) @fputcsv($handle, $data, $this->delimiter, $this->enclosure);
        flock($handle, \LOCK_UN);

        return $success;
    }

    /**
     * Closes the previously opened file handle.
     *
     * @return bool
     */
    public function closeHandle()
    {
        if (is_resource($this->handle)) {
            return fclose($this->handle);
        }

        return true;
    }

    /**
     * Dumps the data array as a string.
     *
     * @return string
     */
    public function dumpData()
    {
        $handle = fopen('php://temp', 'w+');

        for ($this->rewind(); $this->valid(); $this->next()) {
            flock($handle, \LOCK_EX);
            fputcsv($handle, $this->current(), $this->delimiter, $this->enclosure);
            flock($handle, \LOCK_UN);
        }

        $dump = '';

        rewind($handle);

        while (!feof($handle)) {
            $dump .= fgets($handle);
        }

        fclose($handle);

        return $dump;
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * Returns the types supported by the Writer using an indexed array - lower keys have higher prevalence.
     *
     * @return array
     */
    public function supportedTypes()
    {
        return array('csv');
    }
}
