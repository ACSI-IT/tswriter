<?php

declare(strict_types=1);

namespace TS\Writer;

use TS\Writer\Event\IterationEvent;
use TS\Writer\Event\WriterEvent;
use TS\Writer\Exception\FileNotSetException;
use TS\Writer\Exception\FilesystemException;

abstract class IterableFileWriter extends FileWriter implements IterableWriterInterface
{
    /** @var array */
    protected $lastLine;

    /** @var int */
    protected $mode = FILE_APPEND;

    /**
     * Morphs the data, so that we can hook into and manipulate each subset of our data array.
     *
     * @return mixed
     */
    protected function morphData()
    {
        $this->setLastLine($this->current());

        $this->eventDispatcher->dispatch(WriterEvents::BEFORE_WRITE, new IterationEvent($this));

        return $this->getLastLine();
    }

    /**
     * Actual line writing logic.
     *
     * @param  mixed $data
     * @return bool
     */
    abstract protected function writeLine($data);

    /**
     * Iterator::current()
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Gets the data array for the current line.
     *
     * @return array
     */
    public function getLastLine()
    {
        return $this->lastLine;
    }

    /**
     * Iterator::key()
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Iterator::next()
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * Iterator::rewind()
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * Sets the data array for the current line.
     *
     * @param  mixed $lastLine
     * @return static
     */
    public function setLastLine($lastLine = null)
    {
        $this->lastLine = $lastLine;

        return $this;
    }

    /**
     * Iterator::valid()
     *
     * @return bool
     */
    public function valid()
    {
        return $this->key() !== null;
    }

    /**
     * Writes a single line.
     *
     * @return bool
     * @throws FileNotSetException
     * @throws FilesystemException
     */
    public function write()
    {
        if (!$this->isFileSet()) {
            throw new FileNotSetException;
        }

        $success = $this->valid();
        $data    = $this->morphData();

        if ($success && $data !== null) {
            $this->eventDispatcher->dispatch(WriterEvents::WRITE, new IterationEvent($this));

            $success = $this->writeLine($data);

            // @codeCoverageIgnoreStart
            if (!$success) {
                throw new FilesystemException(
                    sprintf("Couldn't write to file [%s].", $this->file)
                );
            }
            // @codeCoverageIgnoreEnd
        }

        return $success;
    }

    /**
     * Writes all data to the previously specified file.
     *
     * @return bool
     * @throws FileNotSetException
     */
    public function writeAll()
    {
        if (!$this->isFileSet()) {
            throw new FileNotSetException;
        }

        $this->eventDispatcher->dispatch(WriterEvents::WRITE_ALL, new WriterEvent($this));

        for ($this->rewind(); $this->valid(); $this->next()) {
            $this->write();
        }

        $this->eventDispatcher->dispatch(WriterEvents::WRITE_COMPLETE, new WriterEvent($this));

        return true;
    }
}
