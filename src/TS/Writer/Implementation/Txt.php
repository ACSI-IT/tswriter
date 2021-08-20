<?php

declare(strict_types=1);

namespace TS\Writer\Implementation;

use TS\Writer\IterableFileWriter;

class Txt extends IterableFileWriter
{
    /**
     * @param  mixed $data
     * @return bool
     */
    protected function writeLine($data)
    {
        return (bool)@file_put_contents($this->file, $data . $this->lineEnding, $this->mode);
    }

    /**
     * @return string
     */
    public function dumpData()
    {
        $dump = '';

        foreach ($this->data as $value) {
            $dump .= (string)$value . $this->lineEnding;
        }

        return $dump;
    }
}
