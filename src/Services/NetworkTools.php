<?php

declare(strict_types=1);

namespace DevSly\Services;

/**
 * Network Tools Service
 *
 * Provides network diagnostics and infrastructure analysis tools
 */
final class NetworkTools extends AbstractService
{
    /**
     * Perform WHOIS lookup for a domain
     *
     * Returns domain registration information including:
     * - Registrar details
     * - Nameservers
     * - Registration and expiry dates
     * - Registrant information
     *
     * @param string $domain Domain name to lookup
     * @return array<string, mixed> WHOIS information
     *
     * @example
     * ```php
     * $whois = $client->network()->whois('example.com');
     * echo $whois['registrar'];
     * ```
     */
    public function whois(string $domain): array
    {
        return $this->httpClient->get('/api/network/whois', [
            'domain' => $domain,
        ]);
    }

    /**
     * Query DNS records for a domain
     *
     * Supports multiple record types:
     * - A: IPv4 addresses
     * - AAAA: IPv6 addresses
     * - MX: Mail exchange servers
     * - TXT: Text records
     * - CNAME: Canonical name records
     * - NS: Nameserver records
     * - SOA: Start of authority
     * - PTR: Pointer records
     *
     * @param string $domain Domain name to query
     * @param string $recordType DNS record type (default: 'A')
     * @return array<string, mixed> DNS records
     *
     * @example
     * ```php
     * $dns = $client->network()->dns('example.com', 'MX');
     * foreach ($dns['records'] as $record) {
     *     echo $record['value'];
     * }
     * ```
     */
    public function dns(string $domain, string $recordType = 'A'): array
    {
        return $this->httpClient->get('/api/network/dns', [
            'domain' => $domain,
            'type' => strtoupper($recordType),
        ]);
    }

    /**
     * Get IP geolocation and organization details
     *
     * Returns:
     * - Country, region, city
     * - Latitude and longitude
     * - ISP and organization
     * - ASN information
     * - Timezone
     *
     * @param string $ip IP address to lookup
     * @return array<string, mixed> Geolocation data
     *
     * @example
     * ```php
     * $geo = $client->network()->ipGeolocation('8.8.8.8');
     * echo $geo['country'] . ', ' . $geo['city'];
     * ```
     */
    public function ipGeolocation(string $ip): array
    {
        return $this->httpClient->get('/api/network/ip-geolocation', [
            'ip' => $ip,
        ]);
    }

    /**
     * Check HTTP status and response timing
     *
     * Returns:
     * - HTTP status code
     * - Response time
     * - Response headers
     * - Redirect information
     *
     * @param string $url URL to check
     * @param string $method HTTP method (default: 'GET')
     * @param array<string, string> $headers Custom headers
     * @return array<string, mixed> HTTP status information
     *
     * @example
     * ```php
     * $status = $client->network()->httpStatus('https://example.com');
     * echo 'Status: ' . $status['status_code'];
     * echo 'Response Time: ' . $status['response_time'] . 'ms';
     * ```
     */
    public function httpStatus(string $url, string $method = 'GET', array $headers = []): array
    {
        $params = [
            'url' => $url,
            'method' => strtoupper($method),
        ];

        if (!empty($headers)) {
            $params['headers'] = $headers;
        }

        return $this->httpClient->post('/api/network/http-status', $params);
    }

    /**
     * Scan ports on a host
     *
     * @param string $host Hostname or IP address
     * @param array<int> $ports Array of port numbers to scan
     * @param int $timeout Timeout in seconds for each port (default: 5)
     * @return array<string, mixed> Port scan results
     *
     * @example
     * ```php
     * $scan = $client->network()->portScan('example.com', [80, 443, 8080]);
     * foreach ($scan['results'] as $port => $status) {
     *     echo "Port $port: " . ($status['open'] ? 'Open' : 'Closed');
     * }
     * ```
     */
    public function portScan(string $host, array $ports, int $timeout = 5): array
    {
        return $this->httpClient->post('/api/network/port-scan', [
            'host' => $host,
            'ports' => $ports,
            'timeout' => $timeout,
        ]);
    }

    /**
     * Validate SSL certificate and check expiry
     *
     * Returns:
     * - Certificate validity
     * - Issuer information
     * - Subject details
     * - Valid from/to dates
     * - Days until expiration
     * - Certificate chain
     *
     * @param string $domain Domain name to check
     * @param int $port Port number (default: 443)
     * @return array<string, mixed> SSL certificate information
     *
     * @example
     * ```php
     * $ssl = $client->network()->sslCertificate('example.com');
     * echo 'Valid: ' . ($ssl['is_valid'] ? 'Yes' : 'No');
     * echo 'Expires in: ' . $ssl['days_until_expiry'] . ' days';
     * echo 'Issuer: ' . $ssl['issuer'];
     * ```
     */
    public function sslCertificate(string $domain, int $port = 443): array
    {
        return $this->httpClient->get('/api/network/ssl-certificate', [
            'domain' => $domain,
            'port' => $port,
        ]);
    }

    /**
     * Perform traceroute to a host
     *
     * @param string $host Hostname or IP address
     * @param int $maxHops Maximum number of hops (default: 30)
     * @return array<string, mixed> Traceroute results
     *
     * @example
     * ```php
     * $trace = $client->network()->traceroute('example.com');
     * foreach ($trace['hops'] as $hop) {
     *     echo "Hop {$hop['number']}: {$hop['ip']} ({$hop['hostname']})";
     * }
     * ```
     */
    public function traceroute(string $host, int $maxHops = 30): array
    {
        return $this->httpClient->post('/api/network/traceroute', [
            'host' => $host,
            'max_hops' => $maxHops,
        ]);
    }

    /**
     * Ping a host
     *
     * @param string $host Hostname or IP address
     * @param int $count Number of ping packets (default: 4)
     * @return array<string, mixed> Ping results
     *
     * @example
     * ```php
     * $ping = $client->network()->ping('example.com');
     * echo 'Average latency: ' . $ping['avg_latency'] . 'ms';
     * echo 'Packet loss: ' . $ping['packet_loss'] . '%';
     * ```
     */
    public function ping(string $host, int $count = 4): array
    {
        return $this->httpClient->post('/api/network/ping', [
            'host' => $host,
            'count' => $count,
        ]);
    }
}
