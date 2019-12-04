<?php

namespace App\Listeners;

use Carbon\CarbonImmutable;
use EricPridham\LaravelTracer\LaravelTracer;
use Illuminate\Database\Events\QueryExecuted;
use OpenTracing\GlobalTracer;

class TraceQuery
{
    /**
     * Handle the event.
     *
     * @param QueryExecuted $event
     * @return void
     */
    public function handle(QueryExecuted $event): void
    {
        // we don't get the actual start time so we just fake it by saying it's
        // the logged time minus the duration
        $finishTime = CarbonImmutable::now();

        /** @var LaravelTracer $tracer */
        $tracer = app(LaravelTracer::class);

        $scope = $tracer->startActiveSpan('query', [
            'start_time' => $finishTime->subMilliseconds($event->time)
        ]);
        $scope->getSpan()->setTag('query', $event->sql);
        $scope->getSpan()->setTag('elapsedTime', $event->time);
        $scope->getSpan()->finish($finishTime);
        $scope->close();
    }
}
