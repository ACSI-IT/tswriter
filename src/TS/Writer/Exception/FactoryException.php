<?php

declare(strict_types=1);

namespace TS\Writer\Exception;

use Exception;
use RuntimeException;

final class FactoryException extends RuntimeException
{
    /**
     * @param  string    $type
     * @param  Exception $previous
     */
    public function __construct($type, Exception $previous = null)
    {
        parent::__construct(
            sprintf("The FileWriterFactory couldn't create a matching Writer for type [%s].", $type),
            0,
            $previous
        );
    }
}
