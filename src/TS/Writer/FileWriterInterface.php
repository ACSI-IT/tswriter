<?php

declare(strict_types=1);

namespace TS\Writer;

interface FileWriterInterface extends WriterInterface
{
    /**
     * Dumps the data array as a string.
     *
     * @return string
     */
    public function dumpData();

    /**
     * Returns the name of the file that should be written to.
     *
     * @return string
     */
    public function getFileName();

    /**
     * Returns the full path of the file that should be written to.
     *
     * @return string
     */
    public function getFilePath();

    /**
     * Checks if a file name has been set already.
     *
     * @return bool
     */
    public function isFileSet();

    /**
     * Sets the mode a file should be accessed with.
     *
     * @param  int $mode
     * @return static
     */
    public function setFileAccessMode($mode = 0);

    /**
     * Sets the path and file that the data should be written to.
     *
     * @param  string $filePath
     * @param  bool   $createDir
     * @return static
     */
    public function setTargetFile($filePath, $createDir = false);
}
