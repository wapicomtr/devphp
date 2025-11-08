<?php

declare(strict_types=1);

namespace DevSly\Services;

/**
 * Developer Tools Service
 *
 * Provides utilities for code manipulation and common developer tasks
 */
final class DeveloperTools extends AbstractService
{
    /**
     * Format JSON string with proper indentation
     *
     * @param string $json JSON string to format
     * @param int $indent Indentation spaces (default: 2)
     * @return array<string, mixed> Formatted JSON
     *
     * @example
     * ```php
     * $formatted = $client->tools()->formatJson('{"name":"John","age":30}');
     * echo $formatted['formatted'];
     * ```
     */
    public function formatJson(string $json, int $indent = 2): array
    {
        return $this->httpClient->post('/api/tools/json/format', [
            'json' => $json,
            'indent' => $indent,
        ]);
    }

    /**
     * Minify JSON string
     *
     * @param string $json JSON string to minify
     * @return array<string, mixed> Minified JSON
     *
     * @example
     * ```php
     * $minified = $client->tools()->minifyJson($jsonString);
     * echo $minified['minified'];
     * ```
     */
    public function minifyJson(string $json): array
    {
        return $this->httpClient->post('/api/tools/json/minify', [
            'json' => $json,
        ]);
    }

    /**
     * Validate JSON syntax
     *
     * @param string $json JSON string to validate
     * @return array<string, mixed> Validation result with errors if any
     *
     * @example
     * ```php
     * $result = $client->tools()->validateJson($jsonString);
     * if ($result['valid']) {
     *     echo 'Valid JSON';
     * } else {
     *     echo 'Error: ' . $result['error'];
     * }
     * ```
     */
    public function validateJson(string $json): array
    {
        return $this->httpClient->post('/api/tools/json/validate', [
            'json' => $json,
        ]);
    }

    /**
     * Encode string to Base64
     *
     * @param string $data String to encode
     * @return array<string, mixed> Base64 encoded string
     *
     * @example
     * ```php
     * $encoded = $client->tools()->base64Encode('Hello World');
     * echo $encoded['encoded']; // SGVsbG8gV29ybGQ=
     * ```
     */
    public function base64Encode(string $data): array
    {
        return $this->httpClient->post('/api/tools/base64/encode', [
            'data' => $data,
        ]);
    }

    /**
     * Decode Base64 string
     *
     * @param string $data Base64 string to decode
     * @return array<string, mixed> Decoded string
     *
     * @example
     * ```php
     * $decoded = $client->tools()->base64Decode('SGVsbG8gV29ybGQ=');
     * echo $decoded['decoded']; // Hello World
     * ```
     */
    public function base64Decode(string $data): array
    {
        return $this->httpClient->post('/api/tools/base64/decode', [
            'data' => $data,
        ]);
    }

    /**
     * Generate hash for a string
     *
     * Supported algorithms:
     * - md5
     * - sha1
     * - sha256
     * - sha512
     * - bcrypt
     *
     * @param string $data String to hash
     * @param string $algorithm Hash algorithm (default: 'sha256')
     * @return array<string, mixed> Generated hash
     *
     * @example
     * ```php
     * $hash = $client->tools()->hash('password123', 'sha256');
     * echo $hash['hash'];
     * ```
     */
    public function hash(string $data, string $algorithm = 'sha256'): array
    {
        return $this->httpClient->post('/api/tools/hash', [
            'data' => $data,
            'algorithm' => strtolower($algorithm),
        ]);
    }

    /**
     * Format SQL query
     *
     * @param string $sql SQL query to format
     * @param array<string, mixed> $options Formatting options
     * @return array<string, mixed> Formatted SQL
     *
     * @example
     * ```php
     * $formatted = $client->tools()->formatSql(
     *     'SELECT * FROM users WHERE id = 1',
     *     ['uppercase' => true, 'indent' => 2]
     * );
     * echo $formatted['formatted'];
     * ```
     */
    public function formatSql(string $sql, array $options = []): array
    {
        $params = ['sql' => $sql];

        if (isset($options['uppercase'])) {
            $params['uppercase'] = $options['uppercase'];
        }

        if (isset($options['indent'])) {
            $params['indent'] = $options['indent'];
        }

        return $this->httpClient->post('/api/tools/sql/format', $params);
    }

    /**
     * Generate UUID (v4)
     *
     * @param int $count Number of UUIDs to generate (default: 1)
     * @return array<string, mixed> Generated UUID(s)
     *
     * @example
     * ```php
     * $uuid = $client->tools()->generateUuid();
     * echo $uuid['uuid'];
     *
     * $uuids = $client->tools()->generateUuid(5);
     * foreach ($uuids['uuids'] as $uuid) {
     *     echo $uuid;
     * }
     * ```
     */
    public function generateUuid(int $count = 1): array
    {
        return $this->httpClient->post('/api/tools/uuid/generate', [
            'count' => $count,
        ]);
    }

    /**
     * Validate UUID
     *
     * @param string $uuid UUID to validate
     * @return array<string, mixed> Validation result
     *
     * @example
     * ```php
     * $result = $client->tools()->validateUuid('550e8400-e29b-41d4-a716-446655440000');
     * if ($result['valid']) {
     *     echo 'Version: ' . $result['version'];
     * }
     * ```
     */
    public function validateUuid(string $uuid): array
    {
        return $this->httpClient->post('/api/tools/uuid/validate', [
            'uuid' => $uuid,
        ]);
    }

    /**
     * Generate QR code
     *
     * @param string $data Data to encode in QR code
     * @param array<string, mixed> $options QR code options
     * @return array<string, mixed> QR code image (base64 encoded)
     *
     * @example
     * ```php
     * $qr = $client->tools()->generateQrCode('https://example.com', [
     *     'size' => 300,
     *     'format' => 'png',
     *     'error_correction' => 'M'
     * ]);
     * echo '<img src="data:image/png;base64,' . $qr['image'] . '">';
     * ```
     */
    public function generateQrCode(string $data, array $options = []): array
    {
        $params = ['data' => $data];

        if (isset($options['size'])) {
            $params['size'] = $options['size'];
        }

        if (isset($options['format'])) {
            $params['format'] = $options['format'];
        }

        if (isset($options['error_correction'])) {
            $params['error_correction'] = $options['error_correction'];
        }

        return $this->httpClient->post('/api/tools/qrcode/generate', $params);
    }

    /**
     * Test regular expression
     *
     * @param string $pattern Regular expression pattern
     * @param string $text Text to test against
     * @param array<string, mixed> $options Regex options
     * @return array<string, mixed> Match results
     *
     * @example
     * ```php
     * $result = $client->tools()->testRegex(
     *     '/\d{3}-\d{3}-\d{4}/',
     *     'Call me at 555-123-4567',
     *     ['global' => true]
     * );
     * if ($result['matches']) {
     *     foreach ($result['results'] as $match) {
     *         echo $match['value'];
     *     }
     * }
     * ```
     */
    public function testRegex(string $pattern, string $text, array $options = []): array
    {
        $params = [
            'pattern' => $pattern,
            'text' => $text,
        ];

        if (isset($options['global'])) {
            $params['global'] = $options['global'];
        }

        if (isset($options['multiline'])) {
            $params['multiline'] = $options['multiline'];
        }

        if (isset($options['case_insensitive'])) {
            $params['case_insensitive'] = $options['case_insensitive'];
        }

        return $this->httpClient->post('/api/tools/regex/test', $params);
    }

    /**
     * Decode JWT token
     *
     * @param string $token JWT token to decode
     * @param bool $verify Verify signature (default: false)
     * @param string|null $secret Secret key for verification
     * @return array<string, mixed> Decoded token data
     *
     * @example
     * ```php
     * $decoded = $client->tools()->decodeJwt($jwtToken);
     * echo 'Issuer: ' . $decoded['payload']['iss'];
     * echo 'Expires: ' . $decoded['payload']['exp'];
     * ```
     */
    public function decodeJwt(string $token, bool $verify = false, ?string $secret = null): array
    {
        $params = [
            'token' => $token,
            'verify' => $verify,
        ];

        if ($verify && $secret !== null) {
            $params['secret'] = $secret;
        }

        return $this->httpClient->post('/api/tools/jwt/decode', $params);
    }

    /**
     * URL encode a string
     *
     * @param string $data String to encode
     * @return array<string, mixed> URL encoded string
     *
     * @example
     * ```php
     * $encoded = $client->tools()->urlEncode('Hello World!');
     * echo $encoded['encoded']; // Hello%20World%21
     * ```
     */
    public function urlEncode(string $data): array
    {
        return $this->httpClient->post('/api/tools/url/encode', [
            'data' => $data,
        ]);
    }

    /**
     * URL decode a string
     *
     * @param string $data URL encoded string to decode
     * @return array<string, mixed> Decoded string
     *
     * @example
     * ```php
     * $decoded = $client->tools()->urlDecode('Hello%20World%21');
     * echo $decoded['decoded']; // Hello World!
     * ```
     */
    public function urlDecode(string $data): array
    {
        return $this->httpClient->post('/api/tools/url/decode', [
            'data' => $data,
        ]);
    }

    /**
     * Generate random string
     *
     * @param int $length String length (default: 16)
     * @param array<string, mixed> $options Generation options
     * @return array<string, mixed> Generated string
     *
     * @example
     * ```php
     * $random = $client->tools()->generateRandomString(32, [
     *     'include_uppercase' => true,
     *     'include_numbers' => true,
     *     'include_symbols' => true
     * ]);
     * echo $random['string'];
     * ```
     */
    public function generateRandomString(int $length = 16, array $options = []): array
    {
        $params = ['length' => $length];

        if (isset($options['include_uppercase'])) {
            $params['include_uppercase'] = $options['include_uppercase'];
        }

        if (isset($options['include_numbers'])) {
            $params['include_numbers'] = $options['include_numbers'];
        }

        if (isset($options['include_symbols'])) {
            $params['include_symbols'] = $options['include_symbols'];
        }

        return $this->httpClient->post('/api/tools/random/string', $params);
    }

    /**
     * Convert timestamp to different formats
     *
     * @param int $timestamp Unix timestamp
     * @param string $targetFormat Target format (iso8601, rfc2822, custom)
     * @param string|null $customFormat Custom format string
     * @return array<string, mixed> Formatted timestamp
     *
     * @example
     * ```php
     * $formatted = $client->tools()->formatTimestamp(time(), 'iso8601');
     * echo $formatted['formatted'];
     * ```
     */
    public function formatTimestamp(int $timestamp, string $targetFormat = 'iso8601', ?string $customFormat = null): array
    {
        $params = [
            'timestamp' => $timestamp,
            'format' => $targetFormat,
        ];

        if ($customFormat !== null) {
            $params['custom_format'] = $customFormat;
        }

        return $this->httpClient->post('/api/tools/timestamp/format', $params);
    }
}
