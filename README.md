# DevSLY PHP SDK

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue.svg)](https://www.php.net/)

Professional, enterprise-grade PHP SDK for the [DevSLY.io](https://devsly.io) API. Built with modern PHP best practices, PSR-4 autoloading, comprehensive error handling, and full type safety.

## Features

### 🌐 Network Tools
- **WHOIS Lookups** - Complete domain registration information
- **DNS Queries** - Support for all record types (A, AAAA, MX, TXT, CNAME, NS, SOA, PTR)
- **IP Geolocation** - Detailed location and ISP information
- **HTTP Status Checks** - Response time and status code monitoring
- **Port Scanning** - Multi-port TCP scanning capabilities
- **SSL Certificate Validation** - Certificate chain verification and expiry tracking
- **Traceroute** - Network path analysis
- **Ping** - Latency and packet loss measurement

### 🚀 Load Testing
- **Test Execution** - Concurrent user simulation with customizable parameters
- **Real-time Monitoring** - Live metrics during test execution
- **Detailed Analytics** - Response time percentiles, throughput, error rates
- **Test Management** - Start, stop, list, and delete tests
- **Multi-region Support** - Distributed load testing capabilities

### 🛠️ Developer Tools
- **JSON Utilities** - Format, minify, and validate JSON
- **Base64 Encoding/Decoding** - Safe data encoding
- **Hash Generation** - MD5, SHA-1, SHA-256, SHA-512, bcrypt
- **SQL Formatting** - Pretty-print SQL queries
- **UUID Generation/Validation** - RFC 4122 compliant UUIDs
- **QR Code Generation** - Customizable QR codes
- **Regex Testing** - Pattern matching and testing
- **JWT Decoding** - Token payload extraction and verification
- **URL Encoding/Decoding** - Safe URL parameter handling
- **Random String Generation** - Cryptographically secure random strings
- **Timestamp Formatting** - Multiple format conversions

### 🔍 Code Analysis
- **Dockerfile Scanning** - Security and best practice analysis
- **Docker Layer Analysis** - Image size optimization insights
- **Dependency Scanning** - Vulnerability detection (npm, composer, pip, maven)
- **Code Quality Metrics** - Complexity, duplicates, maintainability
- **Secret Detection** - API keys, passwords, tokens, credentials
- **API Specification Analysis** - OpenAPI/Swagger validation
- **License Compliance** - License compatibility checking
- **Documentation Generation** - Auto-generate code documentation

## Installation

Install via Composer:

```bash
composer require devsly/php-sdk
```

## Requirements

- PHP 7.4 or higher
- ext-json
- ext-curl

## Quick Start

### Basic Usage

```php
<?php

require_once 'vendor/autoload.php';

use DevSly\Client;

// Create client instance
$client = new Client('your-api-key-here');

// WHOIS lookup
$whois = $client->network()->whois('example.com');
echo "Registrar: " . $whois['registrar'];

// Format JSON
$formatted = $client->tools()->formatJson('{"key":"value"}');
echo $formatted['formatted'];

// Start load test
$test = $client->loadTesting()->start('https://example.com', 100, 60);
echo "Test ID: " . $test['test_id'];
```

### Configuration Options

```php
<?php

use DevSly\Client;

// From environment variable (DEVSLY_API_KEY)
$client = Client::fromEnvironment();

// With custom configuration
$client = new Client('your-api-key', [
    'base_url' => 'https://devsly.io',
    'timeout' => 30,                    // Request timeout in seconds
    'connect_timeout' => 10,            // Connection timeout in seconds
    'retry_attempts' => 3,              // Number of retry attempts
    'verify_ssl' => true,               // SSL verification
    'user_agent' => 'MyApp/1.0',       // Custom user agent
    'custom_headers' => [               // Additional headers
        'X-Custom-Header' => 'value',
    ],
]);
```

## Usage Examples

### Network Tools

#### WHOIS Lookup

```php
$whois = $client->network()->whois('example.com');

echo "Registrar: " . $whois['registrar'] . "\n";
echo "Created: " . $whois['created_date'] . "\n";
echo "Expires: " . $whois['expiry_date'] . "\n";
```

#### DNS Query

```php
// Query A records
$dns = $client->network()->dns('example.com', 'A');
foreach ($dns['records'] as $record) {
    echo $record['value'] . "\n";
}

// Query MX records
$mx = $client->network()->dns('example.com', 'MX');
foreach ($mx['records'] as $record) {
    echo "{$record['priority']} {$record['value']}\n";
}
```

#### IP Geolocation

```php
$geo = $client->network()->ipGeolocation('8.8.8.8');

echo "Location: {$geo['city']}, {$geo['country']}\n";
echo "ISP: {$geo['isp']}\n";
echo "Coordinates: {$geo['latitude']}, {$geo['longitude']}\n";
```

#### SSL Certificate Check

```php
$ssl = $client->network()->sslCertificate('example.com');

echo "Valid: " . ($ssl['is_valid'] ? 'Yes' : 'No') . "\n";
echo "Issuer: {$ssl['issuer']}\n";
echo "Expires in: {$ssl['days_until_expiry']} days\n";
```

#### Port Scanning

```php
$scan = $client->network()->portScan('example.com', [80, 443, 8080, 3306]);

foreach ($scan['results'] as $port => $result) {
    $status = $result['open'] ? 'Open' : 'Closed';
    echo "Port {$port}: {$status}\n";
}
```

### Load Testing

#### Start and Monitor Test

```php
// Start load test
$test = $client->loadTesting()->start(
    'https://api.example.com/endpoint',
    users: 100,
    duration: 60,
    options: [
        'method' => 'POST',
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode(['test' => true]),
        'ramp_up' => 10,
    ]
);

$testId = $test['test_id'];

// Monitor progress
while (true) {
    $status = $client->loadTesting()->status($testId);
    echo "Progress: {$status['progress']}%\n";

    if ($status['state'] === 'completed') {
        break;
    }

    sleep(5);
}

// Get results
$results = $client->loadTesting()->results($testId);

echo "Total Requests: {$results['total_requests']}\n";
echo "Success Rate: {$results['success_rate']}%\n";
echo "Avg Response Time: {$results['response_time']['avg']}ms\n";
echo "P95: {$results['percentiles']['p95']}ms\n";
echo "P99: {$results['percentiles']['p99']}ms\n";
```

### Developer Tools

#### JSON Operations

```php
// Format JSON
$formatted = $client->tools()->formatJson('{"name":"John","age":30}', indent: 4);
echo $formatted['formatted'];

// Validate JSON
$validation = $client->tools()->validateJson($jsonString);
if (!$validation['valid']) {
    echo "Error: " . $validation['error'];
}

// Minify JSON
$minified = $client->tools()->minifyJson($jsonString);
```

#### Hash Generation

```php
// SHA-256 hash
$hash = $client->tools()->hash('password123', 'sha256');
echo $hash['hash'];

// Bcrypt hash
$bcrypt = $client->tools()->hash('password123', 'bcrypt');
echo $bcrypt['hash'];
```

#### UUID Generation

```php
// Single UUID
$uuid = $client->tools()->generateUuid();
echo $uuid['uuid'];

// Multiple UUIDs
$uuids = $client->tools()->generateUuid(count: 5);
foreach ($uuids['uuids'] as $id) {
    echo $id . "\n";
}

// Validate UUID
$validation = $client->tools()->validateUuid('550e8400-e29b-41d4-a716-446655440000');
if ($validation['valid']) {
    echo "Valid UUID (version {$validation['version']})";
}
```

#### QR Code Generation

```php
$qr = $client->tools()->generateQrCode('https://example.com', [
    'size' => 300,
    'format' => 'png',
    'error_correction' => 'M'
]);

// Save to file
file_put_contents('qrcode.png', base64_decode($qr['image']));

// Display in HTML
echo '<img src="data:image/png;base64,' . $qr['image'] . '">';
```

#### JWT Decoding

```php
$jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...';
$decoded = $client->tools()->decodeJwt($jwt);

echo "Algorithm: " . $decoded['header']['alg'] . "\n";
echo "Subject: " . $decoded['payload']['sub'] . "\n";
echo "Expires: " . date('Y-m-d H:i:s', $decoded['payload']['exp']) . "\n";
```

### Code Analysis

#### Dockerfile Scanning

```php
$dockerfile = file_get_contents('./Dockerfile');
$analysis = $client->codeAnalysis()->scanDockerfile($dockerfile, [
    'severity_threshold' => 'medium',
    'include_suggestions' => true
]);

foreach ($analysis['issues'] as $issue) {
    echo "[{$issue['severity']}] Line {$issue['line']}: {$issue['message']}\n";
    if (isset($issue['suggestion'])) {
        echo "  Fix: {$issue['suggestion']}\n";
    }
}
```

#### Dependency Vulnerability Scanning

```php
$packageJson = file_get_contents('./package.json');
$vulnerabilities = $client->codeAnalysis()->scanDependencies($packageJson, 'npm');

foreach ($vulnerabilities['vulnerabilities'] as $vuln) {
    echo "[{$vuln['severity']}] {$vuln['package']}\n";
    echo "  CVE: {$vuln['cve']}\n";
    echo "  Current: {$vuln['current_version']}\n";
    echo "  Fixed in: {$vuln['fixed_version']}\n";
}
```

#### Secret Detection

```php
$code = file_get_contents('./config.php');
$secrets = $client->codeAnalysis()->detectSecrets($code);

foreach ($secrets['findings'] as $secret) {
    echo "[{$secret['type']}] Found at line {$secret['line']}\n";
    echo "  Confidence: {$secret['confidence']}\n";
}
```

#### Code Quality Analysis

```php
$code = file_get_contents('./MyClass.php');
$metrics = $client->codeAnalysis()->analyzeCodeQuality($code, 'php', [
    'check_complexity' => true,
    'check_duplicates' => true,
    'check_style' => true
]);

echo "Lines of Code: {$metrics['loc']}\n";
echo "Cyclomatic Complexity: {$metrics['complexity']['cyclomatic']}\n";
echo "Maintainability Index: {$metrics['maintainability_index']}\n";
echo "Duplicate Lines: {$metrics['duplicates']}\n";
```

## Error Handling

The SDK provides comprehensive exception handling:

```php
use DevSly\Client;
use DevSly\Exceptions\AuthenticationException;
use DevSly\Exceptions\RateLimitException;
use DevSly\Exceptions\ValidationException;
use DevSly\Exceptions\NetworkException;
use DevSly\Exceptions\ApiException;
use DevSly\Exceptions\DevSlyException;

try {
    $client = new Client('your-api-key');
    $result = $client->network()->whois('example.com');

} catch (AuthenticationException $e) {
    // Invalid API key or unauthorized
    echo "Authentication failed: " . $e->getMessage();

} catch (RateLimitException $e) {
    // Rate limit exceeded
    echo "Rate limit exceeded. Please wait or upgrade your plan.";

} catch (ValidationException $e) {
    // Invalid request parameters
    echo "Invalid parameters: " . $e->getMessage();

} catch (NetworkException $e) {
    // Network-level errors (timeouts, connection failures)
    echo "Network error: " . $e->getMessage();

} catch (ApiException $e) {
    // General API errors
    echo "API error: " . $e->getMessage();

} catch (DevSlyException $e) {
    // All other SDK exceptions
    echo "SDK error: " . $e->getMessage();
}
```

## Exception Hierarchy

```
Exception
└── DevSlyException (base SDK exception)
    ├── ConfigurationException
    ├── NetworkException
    └── ApiException
        ├── AuthenticationException
        ├── RateLimitException
        └── ValidationException
```

## Advanced Features

### Automatic Retry Logic

The SDK automatically retries failed requests with exponential backoff:

```php
$client = new Client('your-api-key', [
    'retry_attempts' => 3,  // Will retry up to 3 times
]);

// Retries: 1s delay, then 2s, then 4s
```

### Custom Headers

```php
$client = new Client('your-api-key', [
    'custom_headers' => [
        'X-Request-ID' => 'unique-request-id',
        'X-App-Version' => '1.0.0',
    ],
]);
```

### SSL Verification

```php
// Disable SSL verification (not recommended for production)
$client = new Client('your-api-key', [
    'verify_ssl' => false,
]);
```

### Timeout Configuration

```php
$client = new Client('your-api-key', [
    'timeout' => 60,           // Total request timeout: 60 seconds
    'connect_timeout' => 15,   // Connection timeout: 15 seconds
]);
```

## Testing

Run the test suite:

```bash
composer test
```

Run PHPStan static analysis:

```bash
composer phpstan
```

Check code style:

```bash
composer cs-check
```

Fix code style:

```bash
composer cs-fix
```

## API Rate Limits

DevSLY API has the following rate limits:

- **Free Plan**: 100 requests/day
- **Pro Plan**: 10,000 requests/day
- **Enterprise Plan**: Unlimited

The SDK automatically handles rate limit responses and throws `RateLimitException` when limits are exceeded.

## Examples

Complete working examples are available in the `examples/` directory:

- `basic_usage.php` - Basic SDK usage and configuration
- `network_tools_example.php` - All network tools examples
- `load_testing_example.php` - Load testing workflow
- `developer_tools_example.php` - Developer utilities
- `code_analysis_example.php` - Code scanning and analysis

## Architecture

### PSR-4 Autoloading

```
DevSly\
├── Client                    # Main client class
├── Config                    # Configuration management
├── Contracts\                # Interfaces
│   ├── HttpClientInterface
│   └── ServiceInterface
├── Http\                     # HTTP layer
│   └── HttpClient
├── Services\                 # API services
│   ├── AbstractService
│   ├── NetworkTools
│   ├── LoadTesting
│   ├── DeveloperTools
│   └── CodeAnalysis
└── Exceptions\               # Exception hierarchy
    ├── DevSlyException
    ├── ConfigurationException
    ├── ApiException
    ├── AuthenticationException
    ├── RateLimitException
    ├── ValidationException
    └── NetworkException
```

### Design Patterns

- **Lazy Loading**: Services are instantiated only when accessed
- **Dependency Injection**: All dependencies are injected via constructor
- **Factory Pattern**: Client acts as a service factory
- **Strategy Pattern**: HTTP client implements interface for testability
- **Exception Hierarchy**: Typed exceptions for precise error handling

## Contributing

Contributions are welcome! Please ensure:

1. All tests pass
2. Code follows PSR-12 coding standards
3. PHPStan analysis passes with no errors
4. New features include tests and documentation

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Links

- [DevSLY.io Website](https://devsly.io)
- [API Documentation](https://devsly.io/api/docs)
- [Dashboard](https://devsly.io/dashboard)
- [Support](https://devsly.io/support)

## Security

If you discover a security vulnerability, please email security@devsly.io instead of using the issue tracker.

## Changelog

### Version 1.0.0 (2024-11-08)

- Initial release
- Network Tools support (WHOIS, DNS, IP Geolocation, HTTP Status, Port Scan, SSL, Traceroute, Ping)
- Load Testing support (Start, Monitor, Results, Management)
- Developer Tools support (JSON, Base64, Hash, SQL, UUID, QR, Regex, JWT, URL, Random, Timestamp)
- Code Analysis support (Dockerfile, Dependencies, Quality, Secrets, API Spec, License, Documentation)
- Comprehensive error handling
- Automatic retry logic
- PSR-4 autoloading
- Full type safety (PHP 7.4+)

---

**Built with ❤️ for developers by developers**
