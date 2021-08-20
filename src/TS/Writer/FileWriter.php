<?php

declare(strict_types=1);

namespace TS\Writer;

use TS\Writer\Event\WriterEvent;
use TS\Writer\Exception\FileNotSetException;
use TS\Writer\Exception\FilesystemException;

abstract class FileWriter extends AbstractWriter implements FileWriterInterface
{
    /** @var string */
    protected $file;

    /** @var string */
    protected $lineEnding = "\n";

    /** @var int */
    protected $mode = 0;

    /** @var int */
    const FILE_MODE_APPEND = \FILE_APPEND;

    /** @var int */
    const FILE_MODE_OVERWRITE = 0;

    /**
     * @return string|null
     */
    public function getFileName()
    {
        if (!$this->isFileSet()) {
            return null;
        }

        $filename = pathinfo($this->file, PATHINFO_FILENAME);
        $ext = pathinfo($this->file, PATHINFO_EXTENSION);

        return $filename . (!empty($ext) ? '.' . $ext : '');
    }

    /**
     * Returns the full path of the file that should be written to.
     *
     * @return string
     */
    public function getFilePath()
    {
        return ($this->isFileSet() ? $this->file : null);
    }

    /**
     * Checks if a file name has been set already.
     *
     * @return bool
     */
    public function isFileSet()
    {
        return $this->file !== null;
    }

    /**
     * Sets the mode a file should be accessed with.
     *
     * @param  int $mode
     * @return static
     */
    public function setFileAccessMode($mode = 0)
    {
        $this->mode = (int)$mode;

        return $this;
    }

    /**
     * Sets the line ending character.
     *
     * @param  string $lineEnding
     * @return static
     */
    public function setLineEnding($lineEnding)
    {
        $this->lineEnding = $lineEnding;

        return $this;
    }

    /**
     * Sets the path and file that the data should be written to.
     *
     * @param  string $filePath
     * @param  bool   $createDir
     * @return static
     * @throws FilesystemException
     */
    public function setTargetFile($filePath, $createDir = false)
    {
        $tarDir = pathinfo($filePath, PATHINFO_DIRNAME);

        if (!file_exists($tarDir)) {
            if ($createDir === false) {
                throw new FilesystemException(
                    sprintf('Path [%s] does not exist.', $tarDir)
                );
            }

            // @codeCoverageIgnoreStart
            if (@mkdir($tarDir, 0755, true) === false) {
                throw new FilesystemException(
                    sprintf('Could not create path [%s].', $tarDir)
                );
            }
        }

        if (!is_writable($tarDir)) {
            throw new FilesystemException(
                sprintf('Path [%s] is not writable.', $tarDir)
            );
        }
        // @codeCoverageIgnoreEnd

        $this->file = $filePath;

        return $this;
    }

    /**
     * Writes all data to the previously specified file.
     *
     * @return bool
     * @throws FileNotSetException
     * @throws FilesystemException
     */
    public function writeAll()
    {
        if (!$this->isFileSet()) {
            throw new FileNotSetException;
        }

        $this->eventDispatcher->dispatch(WriterEvents::BEFORE_WRITE, new WriterEvent($this));

        $data = $this->dumpData();

        // Save file
        $success = (bool)@file_put_contents($this->file, $data, $this->mode);

        if ($success === false) {
            // @codeCoverageIgnoreStart
            throw new FilesystemException(
                sprintf("Couldn't write to file [%s].", $this->file)
            );
            // @codeCoverageIgnoreEnd
        }

        $this->eventDispatcher->dispatch(WriterEvents::WRITE_COMPLETE, new WriterEvent($this));

        return true;
    }
}
