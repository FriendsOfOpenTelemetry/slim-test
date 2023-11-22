# OpenTelemetry PHP & Nix

> This is a test repository.

# Usage

1. Setup environment by allowing `.envrc` file with `direnv allow`
2. Install Composer dependencies with `composer install`

## Trace

Run `php -S localhost:8000 trace.php`

Then navigate to <http://localhost:8000>.

## Metric

Run `php -S localhost:8001 metric.php` 

Then navigate to <http://localhost:8001>.

# Explore traces & metrics

Run `docker-compose up -d` and navigate to <http://localhost:3000/explore>.

- Exported metrics by Otel Collector: <http://localhost:8889/metrics>
- Collected metrics by Prometheus: <http://localhost:9090>