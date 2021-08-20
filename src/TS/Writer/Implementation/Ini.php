<?php

declare(strict_types=1);

namespace TS\Writer\Implementation;

use TS\Writer\Exception\DumpingException;
use TS\Writer\FileWriter;

class Ini extends FileWriter
{
    /** @var bool */
    private $createSections = false;

    /**
     * Encode the data array to a ini compatible string.
     *
     * @param  mixed $data
     * @param  int   $stackLevel
     * @return string
     * @throws DumpingException
     */
    protected function encodeData($data, $stackLevel = 0)
    {
        $ini = '';

        if (is_array($data)) {
            if ($this->createSections === true) {
                $ini .= $this->encodeSectioned($data, $stackLevel);
            } else {
                $ini .= $this->encodeFlat($data, $stackLevel);
            }
        } elseif (is_string($data)) {
            $ini = '"' . $data . '"';
        } elseif (is_bool($data)) {
            $ini = ($data === true ? 'On' : 'Off');
        } elseif (is_int($data) || is_float($data)) {
            $ini = (string)$data;
        } elseif ($data === null) {
            $ini = '';
        } else {
            throw new DumpingException('Type ' . gettype($data) . " can't be converted to ini.");
        }

        return $ini;
    }

    /**
     * Encode the array to a flat ini string.
     *
     * @param  array $data
     * @param  int   $stackLevel
     * @return string
     * @throws DumpingException
     */
    protected function encodeFlat($data, $stackLevel = 0)
    {
        $ini = '';

        if ($stackLevel++ === 0) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subvalue) {
                        $ini .= $key . '[] = ' . $this->encodeData($subvalue, $stackLevel) . $this->lineEnding;
                    }
                } else {
                    $ini .= $key . ' = ' . $this->encodeData($value, $stackLevel) . $this->lineEnding;
                }
            }
        } else {
            throw new DumpingException('Array stack size is too deep, values can only be flat arrays.');
        }

        return $ini;
    }

    /**
     * Encode the array to sectioned ini string.
     *
     * @param  array $data
     * @param  int   $stackLevel
     * @return string
     * @throws DumpingException
     */
    protected function encodeSectioned($data, $stackLevel = 0)
    {
        $ini = '';

        if ($stackLevel++ <= 1) {
            foreach ($data as $section => $values) {
                if (!is_string($section) || !is_array($values)) {
                    throw new DumpingException(
                        "Sectioned ini data must have the following \$data format:\n
                        \$data = array(/* string */ \$section => array(/* string */ \$key => \$value, ...))."
                    );
                }

                $ini .= '[' . $section . ']' . $this->lineEnding;

                foreach ($values as $key => $value) {
                    if (!is_string($key)) {
                        throw new DumpingException('$key must be a string.');
                    }

                    if (is_array($value)) {
                        foreach ($value as $subValue) {
                            $ini .= $key . '[] = ' . $this->encodeData($subValue, ++$stackLevel) . $this->lineEnding;
                        }
                    } else {
                        $ini .= $key . ' = ' . $this->encodeData($value, ++$stackLevel) . $this->lineEnding;
                    }
                }
            }
        } else {
            throw new DumpingException('Array stack size is too deep, a section can only contain another flat array.');
        }

        return $ini;
    }

    /**
     * Sets whether to create sectioned ini files or not.
     *
     * @param  bool $createSections
     * @return static
     */
    public function createSections($createSections = true)
    {
        $this->createSections = $createSections;

        return $this;
    }

    /**
     * Dumps the data array as a string.
     *
     * @return string
     */
    public function dumpData()
    {
        return $this->encodeData($this->data);
    }
}
