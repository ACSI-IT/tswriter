<?php

declare(strict_types=1);

namespace TS\Writer;

abstract class WriterEvents
{
    /**
     * Dispatched before the writer tries to write.
     * Arguments:
     * - Writer instance
     *
     * @var string
     */
    const BEFORE_WRITE = 'writer.before_write';

    /**
     * Dispatched when the writer is instantiated.
     * Arguments:
     * - Writer instance
     *
     * @var string
     */
    const INIT = 'writer.init';

    /**
     * Dispatched when a line write occurs.
     * Arguments:
     * - Writer instance
     * - Data array
     *
     * @var string
     */
    const WRITE = 'writer.write';

    /**
     * Dispatched when a writer's writeAll() method is called.
     * Arguments:
     * - Writer instance
     *
     * @var string
     */
    const WRITE_ALL = 'writer.write_all';

    /**
     * Dispatched when the writer has finished writing.
     * Arguments:
     * - Writer instance
     *
     * @var string
     */
    const WRITE_COMPLETE = 'writer.write_complete';
}
