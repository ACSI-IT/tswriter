<?php

declare(strict_types=1);

namespace TS\Writer\Implementation;

use Symfony\Component\Yaml\Dumper;
use TS\Writer\FileWriter;

class Yaml extends FileWriter
{
    /** @var int */
    private $indentation = 4;

    /** @var int */
    private $inlineLevel = 3;

    /**
     * Dumps the data array as a string.
     *
     * @return string
     */
    public function dumpData()
    {
        $dumper = new Dumper();
        $dumper->setIndentation($this->indentation);

        return $dumper->dump($this->data, $this->inlineLevel);
    }

    /**
     * Sets the number of spaces used for indentation.
     *
     * @param  int $indentation
     * @return static
     */
    public function setIndentation($indentation = 4)
    {
        $this->indentation = $indentation;

        return $this;
    }

    /**
     * Sets the nesting level where data gets displayed inline.
     *
     * @param  int $inlineLevel
     * @return static
     */
    public function setInlineLevel($inlineLevel = 3)
    {
        $this->inlineLevel = $inlineLevel;

        return $this;
    }
}
