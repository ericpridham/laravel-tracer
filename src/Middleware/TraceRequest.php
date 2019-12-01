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
        $tracer = $this->app->make(LaravelTracer::class);
        $tracer->start($request);
//
//        register_shutdown_function(function () use () {
//            $scope->close();
//            $tracer->flush();
//        });

        return $next($request);
    }

    public function terminate()
    {
        $tracer = $this->app->make(LaravelTracer::class);
        $tracer->stop();
    }
}
