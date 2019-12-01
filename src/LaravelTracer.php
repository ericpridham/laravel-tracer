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
    }

    public function registerTransport(TransportInterface $transport): void
    {
        $this->tracer->registerTransport($transport);
    }

    public function start(Request $request): void
    {
        GlobalTracer::set($this->tracer);

        $scope = $this->tracer->startActiveSpan('app');

        $scope->getSpan()->setTag('request.host', $request->getHost());
        $scope->getSpan()->setTag('request.path', $request->path());

        if ($request->user()) {
            $scope->getSpan()->setTag('app.userId', $request->user()->id);
            $scope->getSpan()->setTag('app.userName', $request->user()->name);
        }
    }

    public function stop()
    {
        $this->rootScope->close();
        $this->tracer->flush();
    }
}