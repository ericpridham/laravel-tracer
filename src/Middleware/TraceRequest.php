<?php


namespace EricPridham\LaravelTracing\Middleware;

use Closure;
use OpenTracing\GlobalTracer;
use OpenTracingClient\Tracer;
use OpenTracingClient\Transport\HoneycombClient;
use OpenTracingClient\Transport\HoneycombTransport;

class TraceRequest
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tracer = new Tracer();
        $tracer->registerTransport(new HoneycombTransport(new HoneycombClient(config('honeycomb.apiKey'), config('honeycomb.dataset'))));

        GlobalTracer::set($tracer);
        $scope = $tracer->startActiveSpan('app');

        $scope->getSpan()->setTag('request.host', $request->getHost());
        $scope->getSpan()->setTag('request.path', $request->path());

        if ($request->user()) {
            $scope->getSpan()->setTag('app.userId', $request->user()->id);
            $scope->getSpan()->setTag('app.userName', $request->user()->name);
        }

        register_shutdown_function(function () use ($scope, $tracer) {
            $scope->close();
            $tracer->flush();
        });

        return $next($request);
    }
}
