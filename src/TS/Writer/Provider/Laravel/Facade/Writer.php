<?php

declare(strict_types=1);

namespace TS\Writer\Provider\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

class Writer extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'writer';
    }
}
