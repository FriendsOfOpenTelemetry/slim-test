<?php

use OpenTelemetry\Contrib\Otlp\MetricExporter;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$httpTransport = (new OtlpHttpTransportFactory())
    ->create('http://localhost:4318/v1/metrics', 'application/json');

$exporter = new MetricExporter($httpTransport);
$reader = new ExportingReader($exporter);

$meterProvider = MeterProvider::builder()
    ->setResource(ResourceInfoFactory::defaultResource())
    ->addReader($reader)
    ->build();

$meter = $meterProvider->getMeter('demo');

$counter = $meter->createCounter('index');

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response) use ($counter, $meterProvider) {
    $result = random_int(1,6);
    $response->getBody()->write((string)$result);
    $counter->add($result);
    $meterProvider->forceFlush();
    return $response;
});

$app->run();