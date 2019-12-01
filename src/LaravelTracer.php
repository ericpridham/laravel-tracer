<?php

namespace EricPridham\LaravelTracing;

use OpenTracingClient\Tracer;
use OpenTracingClient\Transport\HoneycombClient;
use OpenTracingClient\Transport\HoneycombTransport;
use OpenTracingClient\TransportInterface;

class LaravelTracer
{
    /**
     * @var Tracer
     */
    private $tracer;

    public function __construct()
    {
        $this->tracer = new Tracer();
    }

    public function registerTransport(TransportInterface $transport): void
    {
        $this->tracer->registerTransport($transport);
    }

    public function start(): void
    {
    }
}