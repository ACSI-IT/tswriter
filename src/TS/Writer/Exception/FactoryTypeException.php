<?php

declare(strict_types=1);

namespace TS\Writer\Exception;

use Exception;
use RuntimeException;

final class FactoryTypeException extends RuntimeException
{
    /**
     * @param  string    $type
     * @param  Exception $previous
     */
    public function __construct($type, Exception $previous = null)
    {
        parent::__construct(
            sprintf("The FileWriterContainer couldn't create a matching writer for type [%s].", $type),
            0,
            $previous
        );
    }
}
