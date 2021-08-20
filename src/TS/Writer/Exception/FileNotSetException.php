<?php

declare(strict_types=1);

namespace TS\Writer\Exception;

use Exception;
use LogicException;

final class FileNotSetException extends LogicException
{
    /**
     * @param Exception $previous
     */
    public function __construct(Exception $previous = null)
    {
        parent::__construct('No file to write to given.', 0, $previous);
    }
}
