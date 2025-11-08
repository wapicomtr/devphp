<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DevSly\Client;
use DevSly\Exceptions\DevSlyException;

$client = new Client('your-api-key-here');

try {
    // JSON Formatting
    echo "=== JSON Formatting ===\n";
    $json = '{"name":"John","age":30,"city":"New York"}';
    $formatted = $client->tools()->formatJson($json);
    echo $formatted['formatted'] . "\n\n";

    // JSON Validation
    echo "=== JSON Validation ===\n";
    $validationResult = $client->tools()->validateJson($json);
    echo "Valid: " . ($validationResult['valid'] ? 'Yes' : 'No') . "\n\n";

    // Base64 Encoding/Decoding
    echo "=== Base64 ===\n";
    $encoded = $client->tools()->base64Encode('Hello, World!');
    echo "Encoded: {$encoded['encoded']}\n";

    $decoded = $client->tools()->base64Decode($encoded['encoded']);
    echo "Decoded: {$decoded['decoded']}\n\n";

    // Hash Generation
    echo "=== Hash Generation ===\n";
    $hash = $client->tools()->hash('password123', 'sha256');
    echo "SHA-256 Hash: {$hash['hash']}\n\n";

    // SQL Formatting
    echo "=== SQL Formatting ===\n";
    $sql = 'SELECT * FROM users WHERE id = 1 AND status = "active"';
    $formattedSql = $client->tools()->formatSql($sql, [
        'uppercase' => true,
        'indent' => 2
    ]);
    echo $formattedSql['formatted'] . "\n\n";

    // UUID Generation
    echo "=== UUID Generation ===\n";
    $uuid = $client->tools()->generateUuid();
    echo "UUID: {$uuid['uuid']}\n";

    // Generate multiple UUIDs
    $uuids = $client->tools()->generateUuid(3);
    echo "Multiple UUIDs:\n";
    foreach ($uuids['uuids'] as $id) {
        echo "  - {$id}\n";
    }
    echo "\n";

    // UUID Validation
    echo "=== UUID Validation ===\n";
    $validation = $client->tools()->validateUuid($uuid['uuid']);
    echo "Valid: " . ($validation['valid'] ? 'Yes' : 'No') . "\n";
    echo "Version: {$validation['version']}\n\n";

    // QR Code Generation
    echo "=== QR Code Generation ===\n";
    $qr = $client->tools()->generateQrCode('https://example.com', [
        'size' => 300,
        'format' => 'png'
    ]);
    echo "QR Code generated (base64): " . substr($qr['image'], 0, 50) . "...\n\n";

    // Regex Testing
    echo "=== Regex Testing ===\n";
    $regexResult = $client->tools()->testRegex(
        '/\d{3}-\d{3}-\d{4}/',
        'Call me at 555-123-4567 or 555-987-6543',
        ['global' => true]
    );
    echo "Matches found: " . ($regexResult['matches'] ? 'Yes' : 'No') . "\n";
    if ($regexResult['matches']) {
        foreach ($regexResult['results'] as $match) {
            echo "  - {$match['value']}\n";
        }
    }
    echo "\n";

    // JWT Decoding
    echo "=== JWT Decoding ===\n";
    $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';
    $decoded = $client->tools()->decodeJwt($jwt);
    echo "Header:\n";
    print_r($decoded['header']);
    echo "Payload:\n";
    print_r($decoded['payload']);
    echo "\n";

    // URL Encoding/Decoding
    echo "=== URL Encoding ===\n";
    $urlEncoded = $client->tools()->urlEncode('Hello World!');
    echo "Encoded: {$urlEncoded['encoded']}\n";

    $urlDecoded = $client->tools()->urlDecode($urlEncoded['encoded']);
    echo "Decoded: {$urlDecoded['decoded']}\n\n";

    // Random String Generation
    echo "=== Random String Generation ===\n";
    $random = $client->tools()->generateRandomString(32, [
        'include_uppercase' => true,
        'include_numbers' => true,
        'include_symbols' => true
    ]);
    echo "Random String: {$random['string']}\n\n";

    // Timestamp Formatting
    echo "=== Timestamp Formatting ===\n";
    $timestamp = $client->tools()->formatTimestamp(time(), 'iso8601');
    echo "ISO 8601: {$timestamp['formatted']}\n";

} catch (DevSlyException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
