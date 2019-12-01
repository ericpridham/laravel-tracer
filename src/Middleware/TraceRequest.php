<?php


namespace EricPridham\LaravelTracer\Middleware;

use Closure;
use EricPridham\LaravelTracer\LaravelTracer;

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
        $tracer = app(LaravelTracer::class);
        $tracer->start($request);

        return $next($request);
    }

    public function terminate()
    {
        $tracer = app(LaravelTracer::class);
        $tracer->stop();
    }
}
