<?php

declare(strict_types=1);

namespace DevSly;

use DevSly\Http\HttpClient;
use DevSly\Services\CodeAnalysis;
use DevSly\Services\DeveloperTools;
use DevSly\Services\LoadTesting;
use DevSly\Services\NetworkTools;

/**
 * DevSLY API Client
 *
 * Main entry point for the DevSLY PHP SDK
 *
 * @example
 * ```php
 * $client = new Client('your-api-key');
 * $whoisData = $client->network()->whois('example.com');
 * ```
 */
final class Client
{
    private Config $config;
    private HttpClient $httpClient;

    private ?NetworkTools $networkTools = null;
    private ?LoadTesting $loadTesting = null;
    private ?DeveloperTools $developerTools = null;
    private ?CodeAnalysis $codeAnalysis = null;

    /**
     * Constructor
     *
     * @param string $apiKey API key for authentication
     * @param array<string, mixed> $options Configuration options
     */
    public function __construct(string $apiKey, array $options = [])
    {
        $this->config = new Config($apiKey, $options);
        $this->httpClient = new HttpClient($this->config);
    }

    /**
     * Create client from environment variables
     *
     * @param array<string, mixed> $options Configuration options
     * @return self
     */
    public static function fromEnvironment(array $options = []): self
    {
        $config = Config::fromEnvironment($options);
        return new self($config->getApiKey(), $options);
    }

    /**
     * Get Network Tools service
     *
     * Provides access to:
     * - WHOIS lookups
     * - DNS queries
     * - IP geolocation
     * - HTTP status checks
     * - Port scanning
     * - SSL certificate validation
     *
     * @return NetworkTools
     */
    public function network(): NetworkTools
    {
        if ($this->networkTools === null) {
            $this->networkTools = new NetworkTools($this->httpClient);
        }

        return $this->networkTools;
    }

    /**
     * Get Load Testing service
     *
     * Provides access to:
     * - Initiating load tests
     * - Monitoring test status
     * - Terminating tests
     * - Retrieving test results
     *
     * @return LoadTesting
     */
    public function loadTesting(): LoadTesting
    {
        if ($this->loadTesting === null) {
            $this->loadTesting = new LoadTesting($this->httpClient);
        }

        return $this->loadTesting;
    }

    /**
     * Get Developer Tools service
     *
     * Provides access to:
     * - JSON formatting
     * - Base64 encoding/decoding
     * - Hash generation
     * - SQL formatting
     * - UUID generation
     * - QR code creation
     * - Regex testing
     * - JWT decoding
     *
     * @return DeveloperTools
     */
    public function tools(): DeveloperTools
    {
        if ($this->developerTools === null) {
            $this->developerTools = new DeveloperTools($this->httpClient);
        }

        return $this->developerTools;
    }

    /**
     * Get Code Analysis service
     *
     * Provides access to:
     * - Dockerfile scanning
     * - Security analysis
     * - Configuration review
     *
     * @return CodeAnalysis
     */
    public function codeAnalysis(): CodeAnalysis
    {
        if ($this->codeAnalysis === null) {
            $this->codeAnalysis = new CodeAnalysis($this->httpClient);
        }

        return $this->codeAnalysis;
    }

    /**
     * Get configuration instance
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Get HTTP client instance
     *
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }
}
