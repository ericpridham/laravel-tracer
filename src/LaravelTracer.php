<?php

namespace EricPridham\LaravelTracer;

use App\Listeners\TraceQuery;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use OpenTracing\GlobalTracer;
use OpenTracing\StartSpanOptions;
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
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
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
        $this->app['events']->listen(QueryExecuted::class, TraceQuery::class);

        GlobalTracer::set($this->tracer);

        $this->rootScope = $this->tracer->startActiveSpan($this->rootName);

        $this->rootScope->getSpan()->setTag('request.host', $request->getHost());
        $this->rootScope->getSpan()->setTag('request.path', $request->path());

        if ($request->user()) {
            $this->rootScope->getSpan()->setTag('app.userId', $request->user()->id);
            $this->rootScope->getSpan()->setTag('app.userName', $request->user()->name);
        }
    }

    /**
     * @param string $name
     * @param StartSpanOptions|array|null $options
     * @return Scope
     */
    public function startActiveSpan(string $name, $options = null): Scope
    {
        return $this->tracer->startActiveSpan($name, $options);
    }

    public function stop()
    {
        $this->rootScope->close();
        $this->tracer->flush();
    }
}