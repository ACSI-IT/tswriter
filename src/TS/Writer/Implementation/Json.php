<?php

declare(strict_types=1);

namespace TS\Writer\Implementation;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TS\Writer\FileWriter;
use Laminas\Json\Json as ZendJson;

class Json extends FileWriter
{
    /** @var bool */
    private $compabilityMode = false;

    /** @var int */
    private $indentation = 4;

    /** @var int */
    private $options = 0;

    /** @var bool */
    private $prettyPrint = true;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->compabilityMode = (version_compare(PHP_VERSION, '5.4.0') <= 0);

        parent::__construct($eventDispatcher);
    }

    /**
     * Returns the options for json_encode, adding JSON_PRETTY_PRINT if pretty printing is enabled.
     *
     * @return int
     */
    private function options()
    {
        $options = $this->options;

        // @codeCoverageIgnoreStart
        if (!$this->compabilityMode && $this->prettyPrint) {
            $options |= 128; // JSON_PRETTY_PRINT
        }
        // @codeCoverageIgnoreEnd

        return $options;
    }

    /**
     * Dumps the data array as a string.
     *
     * @return string
     */
    public function dumpData()
    {
        $json = json_encode($this->data, $this->options());

        // @codeCoverageIgnoreStart
        if ($this->compabilityMode && $this->prettyPrint) {
            $json = ZendJson::prettyPrint(
                $json,
                array(
                    'indent' => str_repeat(' ', $this->indentation)
                )
            );
        }
        // @codeCoverageIgnoreEnd

        return $json;
    }

    /**
     * Sets the number of spaces used for indentation.
     *
     * @param  int $indentation
     * @return $this
     */
    public function setIndentation($indentation = 4)
    {
        $this->indentation = $indentation;

        return $this;
    }

    /**
     * Sets the given $option flag, adding or removing it from the internal flag set.
     *
     * @param  int $option
     * @param  bool $enable
     * @return $this
     */
    public function setOption($option, $enable = true)
    {
        if (!$this->compabilityMode && $option == 128 /* JSON_PRETTY_PRINT */) {
            return $this->setPrettyPrint($enable);
        }

        if ($enable === true) {
            $this->options |= (int)$option;
        } else {
            $this->options &= (int)~$option;
        }

        return $this;
    }

    /**
     * Whether or not to use pretty printing.
     *
     * @param  bool $prettyPrint
     * @return $this
     */
    public function setPrettyPrint($prettyPrint = true)
    {
        $this->prettyPrint = $prettyPrint;

        return $this;
    }
}
