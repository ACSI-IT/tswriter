<?php

declare(strict_types=1);

namespace TS\Writer\Exception;

use Exception;
use RuntimeException;

final class FactoryClassException extends RuntimeException
{
    /**
     * @param  string    $class
     * @param  Exception $previous
     */
    public function __construct($class, Exception $previous = null)
    {
        parent::__construct(
            sprintf("The FileWriterContainer couldn't create a matching writer for class [%s].", $class),
            0,
            $previous
        );
    }
}
