<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DevSly\Client;
use DevSly\Exceptions\AuthenticationException;
use DevSly\Exceptions\RateLimitException;
use DevSly\Exceptions\DevSlyException;

// Method 1: Direct instantiation
$client = new Client('your-api-key-here');

// Method 2: Using environment variable (DEVSLY_API_KEY)
// $client = Client::fromEnvironment();

// Method 3: With custom configuration
$client = new Client('your-api-key-here', [
    'base_url' => 'https://devsly.io',
    'timeout' => 30,
    'connect_timeout' => 10,
    'retry_attempts' => 3,
    'verify_ssl' => true,
    'custom_headers' => [
        'X-Custom-Header' => 'value',
    ],
]);

try {
    // Network Tools - WHOIS Lookup
    $whois = $client->network()->whois('example.com');
    echo "Domain: example.com\n";
    echo "Registrar: {$whois['registrar']}\n\n";

    // Developer Tools - JSON Formatting
    $formatted = $client->tools()->formatJson('{"key":"value"}');
    echo "Formatted JSON:\n{$formatted['formatted']}\n\n";

    // Load Testing - Start a test
    $test = $client->loadTesting()->start('https://example.com', 50, 30);
    echo "Load test started with ID: {$test['test_id']}\n\n";

    // Code Analysis - Scan Dockerfile
    $dockerfile = file_get_contents('./Dockerfile');
    $analysis = $client->codeAnalysis()->scanDockerfile($dockerfile);
    echo "Dockerfile issues found: " . count($analysis['issues']) . "\n";

} catch (AuthenticationException $e) {
    // Handle authentication errors
    echo "Authentication failed: " . $e->getMessage() . "\n";
    echo "Please check your API key.\n";

} catch (RateLimitException $e) {
    // Handle rate limit errors
    echo "Rate limit exceeded: " . $e->getMessage() . "\n";
    echo "Please upgrade your plan or wait before making more requests.\n";

} catch (DevSlyException $e) {
    // Handle all other DevSLY exceptions
    echo "API Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";

} catch (\Exception $e) {
    // Handle unexpected errors
    echo "Unexpected error: " . $e->getMessage() . "\n";
}
