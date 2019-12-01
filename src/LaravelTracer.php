<?php

namespace EricPridham\LaravelTracer;

use Illuminate\Http\Request;
use OpenTracing\GlobalTracer;
use OpenTracingClient\Scope;
use OpenTracingClient\Tracer;
use OpenTracingClient\TransportInterface;

class LaravelTracer
{
    /**
     * @var Tracer
     */
    private $tracer;
    /**
     * @var Scope
     */
    private $rootScope;

    public function __construct()
    {
        $this->tracer = new Tracer();
        $this->rootName = 'app';
    }

    public function registerTransport(TransportInterface $transport): void
    {
        $this->tracer->registerTransport($transport);
    }

    public function setRootName(string $name)
    {
        $this->rootName = $name;
    }

    public function start(Request $request): void
    {
        GlobalTracer::set($this->tracer);

        $this->rootScope = $this->tracer->startActiveSpan($this->rootName);

        $this->rootScope->getSpan()->setTag('request.host', $request->getHost());
        $this->rootScope->getSpan()->setTag('request.path', $request->path());

        if ($request->user()) {
            $this->rootScope->getSpan()->setTag('app.userId', $request->user()->id);
            $this->rootScope->getSpan()->setTag('app.userName', $request->user()->name);
        }
    }

    public function stop()
    {
        $this->rootScope->close();
        $this->tracer->flush();
    }
}