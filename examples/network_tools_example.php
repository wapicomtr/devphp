<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DevSly\Client;
use DevSly\Exceptions\DevSlyException;

// Create client instance
$client = new Client('your-api-key-here');

// Alternatively, use environment variable
// $client = Client::fromEnvironment();

try {
    // WHOIS Lookup
    echo "=== WHOIS Lookup ===\n";
    $whois = $client->network()->whois('example.com');
    echo "Registrar: " . $whois['registrar'] . "\n";
    echo "Created: " . $whois['created_date'] . "\n\n";

    // DNS Query
    echo "=== DNS Query ===\n";
    $dns = $client->network()->dns('example.com', 'A');
    foreach ($dns['records'] as $record) {
        echo "A Record: " . $record['value'] . "\n";
    }
    echo "\n";

    // IP Geolocation
    echo "=== IP Geolocation ===\n";
    $geo = $client->network()->ipGeolocation('8.8.8.8');
    echo "Location: {$geo['city']}, {$geo['country']}\n";
    echo "ISP: {$geo['isp']}\n\n";

    // HTTP Status Check
    echo "=== HTTP Status Check ===\n";
    $status = $client->network()->httpStatus('https://example.com');
    echo "Status Code: {$status['status_code']}\n";
    echo "Response Time: {$status['response_time']}ms\n\n";

    // Port Scan
    echo "=== Port Scan ===\n";
    $scan = $client->network()->portScan('example.com', [80, 443, 8080]);
    foreach ($scan['results'] as $port => $result) {
        $status = $result['open'] ? 'Open' : 'Closed';
        echo "Port {$port}: {$status}\n";
    }
    echo "\n";

    // SSL Certificate Check
    echo "=== SSL Certificate ===\n";
    $ssl = $client->network()->sslCertificate('example.com');
    echo "Valid: " . ($ssl['is_valid'] ? 'Yes' : 'No') . "\n";
    echo "Issuer: {$ssl['issuer']}\n";
    echo "Expires in: {$ssl['days_until_expiry']} days\n\n";

    // Ping
    echo "=== Ping ===\n";
    $ping = $client->network()->ping('example.com', 4);
    echo "Average Latency: {$ping['avg_latency']}ms\n";
    echo "Packet Loss: {$ping['packet_loss']}%\n\n";

} catch (DevSlyException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
