<?php

declare(strict_types=1);

namespace TS\Writer\Exception;

use Exception;
use RuntimeException;

final class DumpingException extends RuntimeException
{
    /**
     * @param string    $message
     * @param Exception $previous
     */
    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
