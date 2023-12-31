<?php

use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

putenv('OTEL_PHP_FIBERS_ENABLED=true');

$httpTransport = (new OtlpHttpTransportFactory())
    ->create('http://localhost:4318/v1/traces', 'application/json');

$exporter = new SpanExporter($httpTransport);

$tracerProvider = new TracerProvider(
    new SimpleSpanProcessor($exporter)
);
$tracer = $tracerProvider->getTracer('demo');

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response) use ($tracer) {
    $span = $tracer
        ->spanBuilder('manual-span')
        ->startSpan();
    $result = random_int(1,6);
    $response->getBody()->write((string)$result);
    $span
        ->addEvent('rolled dice', ['result' => $result])
        ->end();
    return $response;
});

$app->run();