<?php

namespace EricPridham\LaravelTracer;

use EricPridham\LaravelTracer\Middleware\TraceRequest;
use Illuminate\Contracts\Http\Kernel;
use OpenTracingClient\Transport\HoneycombClient;
use OpenTracingClient\Transport\HoneycombTransport;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    private const CONFIG_FILE = __DIR__ . '/../config/laraveltracer.php';

    public function register()
    {
        $this->mergeConfigFrom(self::CONFIG_FILE, 'laraveltracer');
        $this->app->singleton(LaravelTracer::class, function () {
            $tracer = new LaravelTracer();

            if (config('laraveltracer.honeycomb.key')) {
                $tracer->registerTransport(
                    new HoneycombTransport(
                        new HoneycombClient(config('laraveltracer.honeycomb.key'), config('laraveltracer.honeycomb.dataset'))
                    )
                );
            }
        });
    }

    public function boot()
    {
        $this->publishes([self::CONFIG_FILE => config_path('laraveltracer.php')]);
        $this->app->make(Kernel::class)->pushMiddleware(TraceRequest::class);
    }
}