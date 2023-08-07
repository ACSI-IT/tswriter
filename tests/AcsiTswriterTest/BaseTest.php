<?php

declare(strict_types=1);

namespace AcsiTswriterTest;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class BaseTest extends TestCase
{
    protected $data = array(
        'array'  => array('key' => 'value'),
        'bool'   => true,
        'float'  => 3.14,
        'int'    => 1,
        'null'   => null,
        'string' => 'value',
    );

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    protected $tmpDir;

    public function __construct()
    {
        parent::__construct();

        $this->dispatcher = new EventDispatcher();
        $this->tmpDir     = __DIR__ . '/tmp';
    }

    protected function getData()
    {
        $data = $this->data;

        if (!isset($data['object'])) {
            $data['object'] = new stdClass;
        }

        return $data;
    }
}
