<?php

declare(strict_types=1);

namespace DevSly\Services;

/**
 * Load Testing Service
 *
 * Provides functionality for performance evaluation and load testing
 */
final class LoadTesting extends AbstractService
{
    /**
     * Initiate a load test
     *
     * Starts a new load test with specified parameters:
     * - Target URL
     * - Number of concurrent users
     * - Duration
     * - Request rate
     *
     * @param string $url Target URL to test
     * @param int $users Number of concurrent users
     * @param int $duration Test duration in seconds
     * @param array<string, mixed> $options Additional options
     * @return array<string, mixed> Test information including test_id
     *
     * @example
     * ```php
     * $test = $client->loadTesting()->start('https://example.com', 100, 60, [
     *     'method' => 'GET',
     *     'headers' => ['User-Agent' => 'LoadTest'],
     *     'ramp_up' => 10, // Ramp up time in seconds
     * ]);
     * $testId = $test['test_id'];
     * ```
     */
    public function start(string $url, int $users, int $duration, array $options = []): array
    {
        $params = [
            'url' => $url,
            'users' => $users,
            'duration' => $duration,
        ];

        // Merge optional parameters
        if (isset($options['method'])) {
            $params['method'] = strtoupper($options['method']);
        }

        if (isset($options['headers'])) {
            $params['headers'] = $options['headers'];
        }

        if (isset($options['body'])) {
            $params['body'] = $options['body'];
        }

        if (isset($options['ramp_up'])) {
            $params['ramp_up'] = $options['ramp_up'];
        }

        if (isset($options['think_time'])) {
            $params['think_time'] = $options['think_time'];
        }

        if (isset($options['regions'])) {
            $params['regions'] = $options['regions'];
        }

        return $this->httpClient->post('/api/load-testing/start', $params);
    }

    /**
     * Get load test status
     *
     * Returns current status of a running or completed test:
     * - Test state (running, completed, failed)
     * - Progress percentage
     * - Current metrics
     * - Estimated time remaining
     *
     * @param string $testId Test identifier
     * @return array<string, mixed> Test status information
     *
     * @example
     * ```php
     * $status = $client->loadTesting()->status($testId);
     * echo 'Status: ' . $status['state'];
     * echo 'Progress: ' . $status['progress'] . '%';
     * echo 'Current RPS: ' . $status['current_rps'];
     * ```
     */
    public function status(string $testId): array
    {
        return $this->httpClient->get('/api/load-testing/status', [
            'test_id' => $testId,
        ]);
    }

    /**
     * Stop a running load test
     *
     * Terminates a currently running test and triggers result compilation
     *
     * @param string $testId Test identifier
     * @return array<string, mixed> Stop confirmation
     *
     * @example
     * ```php
     * $result = $client->loadTesting()->stop($testId);
     * echo $result['message']; // "Test stopped successfully"
     * ```
     */
    public function stop(string $testId): array
    {
        return $this->httpClient->post('/api/load-testing/stop', [
            'test_id' => $testId,
        ]);
    }

    /**
     * Get detailed test results
     *
     * Returns comprehensive test results including:
     * - Request statistics (total, success, failed)
     * - Response time metrics (min, max, avg, percentiles)
     * - Throughput (requests per second)
     * - Error rate and error details
     * - Resource utilization
     *
     * @param string $testId Test identifier
     * @return array<string, mixed> Detailed test results
     *
     * @example
     * ```php
     * $results = $client->loadTesting()->results($testId);
     * echo 'Total Requests: ' . $results['total_requests'];
     * echo 'Success Rate: ' . $results['success_rate'] . '%';
     * echo 'Avg Response Time: ' . $results['avg_response_time'] . 'ms';
     * echo 'P95: ' . $results['percentiles']['p95'] . 'ms';
     * echo 'P99: ' . $results['percentiles']['p99'] . 'ms';
     * ```
     */
    public function results(string $testId): array
    {
        return $this->httpClient->get('/api/load-testing/results', [
            'test_id' => $testId,
        ]);
    }

    /**
     * List all load tests
     *
     * Returns a list of all load tests for the current API key
     *
     * @param int $limit Number of tests to return (default: 20)
     * @param int $offset Pagination offset (default: 0)
     * @param string|null $status Filter by status (running, completed, failed)
     * @return array<string, mixed> List of tests
     *
     * @example
     * ```php
     * $tests = $client->loadTesting()->list(50, 0, 'completed');
     * foreach ($tests['tests'] as $test) {
     *     echo $test['test_id'] . ': ' . $test['url'];
     * }
     * ```
     */
    public function list(int $limit = 20, int $offset = 0, ?string $status = null): array
    {
        $params = [
            'limit' => $limit,
            'offset' => $offset,
        ];

        if ($status !== null) {
            $params['status'] = $status;
        }

        return $this->httpClient->get('/api/load-testing/list', $params);
    }

    /**
     * Delete a load test
     *
     * Removes test data and results
     *
     * @param string $testId Test identifier
     * @return array<string, mixed> Deletion confirmation
     *
     * @example
     * ```php
     * $result = $client->loadTesting()->delete($testId);
     * echo $result['message'];
     * ```
     */
    public function delete(string $testId): array
    {
        return $this->httpClient->delete('/api/load-testing/delete', [
            'test_id' => $testId,
        ]);
    }

    /**
     * Get real-time metrics during test execution
     *
     * Returns live metrics for a running test:
     * - Current requests per second
     * - Active users
     * - Response time trends
     * - Error rate
     *
     * @param string $testId Test identifier
     * @return array<string, mixed> Real-time metrics
     *
     * @example
     * ```php
     * $metrics = $client->loadTesting()->metrics($testId);
     * echo 'Current RPS: ' . $metrics['current_rps'];
     * echo 'Active Users: ' . $metrics['active_users'];
     * echo 'Avg Response Time: ' . $metrics['avg_response_time'] . 'ms';
     * ```
     */
    public function metrics(string $testId): array
    {
        return $this->httpClient->get('/api/load-testing/metrics', [
            'test_id' => $testId,
        ]);
    }
}
