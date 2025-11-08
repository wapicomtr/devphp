<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DevSly\Client;
use DevSly\Exceptions\DevSlyException;

$client = new Client('your-api-key-here');

try {
    // Start a load test
    echo "=== Starting Load Test ===\n";
    $test = $client->loadTesting()->start(
        'https://example.com/api/endpoint',
        100, // 100 concurrent users
        60,  // 60 seconds duration
        [
            'method' => 'GET',
            'headers' => [
                'User-Agent' => 'LoadTest/1.0',
            ],
            'ramp_up' => 10, // Ramp up over 10 seconds
        ]
    );

    $testId = $test['test_id'];
    echo "Test ID: {$testId}\n";
    echo "Status: {$test['status']}\n\n";

    // Monitor test progress
    echo "=== Monitoring Test ===\n";
    $startTime = time();
    while (true) {
        $status = $client->loadTesting()->status($testId);

        echo "\rProgress: {$status['progress']}% | ";
        echo "Current RPS: {$status['current_rps']} | ";
        echo "State: {$status['state']}";

        if ($status['state'] === 'completed' || $status['state'] === 'failed') {
            echo "\n";
            break;
        }

        // Update every 5 seconds
        sleep(5);

        // Safety timeout (70 seconds)
        if (time() - $startTime > 70) {
            echo "\n\nTimeout reached, stopping test...\n";
            $client->loadTesting()->stop($testId);
            break;
        }
    }

    // Get detailed results
    echo "\n=== Test Results ===\n";
    $results = $client->loadTesting()->results($testId);

    echo "Total Requests: {$results['total_requests']}\n";
    echo "Successful Requests: {$results['successful_requests']}\n";
    echo "Failed Requests: {$results['failed_requests']}\n";
    echo "Success Rate: {$results['success_rate']}%\n\n";

    echo "Response Times:\n";
    echo "  Min: {$results['response_time']['min']}ms\n";
    echo "  Max: {$results['response_time']['max']}ms\n";
    echo "  Avg: {$results['response_time']['avg']}ms\n";
    echo "  P50: {$results['percentiles']['p50']}ms\n";
    echo "  P95: {$results['percentiles']['p95']}ms\n";
    echo "  P99: {$results['percentiles']['p99']}ms\n\n";

    echo "Throughput: {$results['requests_per_second']} RPS\n";
    echo "Error Rate: {$results['error_rate']}%\n\n";

    // List all tests
    echo "=== All Tests ===\n";
    $allTests = $client->loadTesting()->list(10, 0, 'completed');
    foreach ($allTests['tests'] as $test) {
        echo "ID: {$test['test_id']} | URL: {$test['url']} | Status: {$test['status']}\n";
    }

} catch (DevSlyException $e) {
    echo "\nError: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
