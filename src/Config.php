<?php

declare(strict_types=1);

namespace DevSly;

use DevSly\Exceptions\ConfigurationException;

/**
 * Configuration Manager
 *
 * Handles all configuration settings for the DevSLY API client
 */
final class Config
{
    private const DEFAULT_BASE_URL = 'https://devsly.io';
    private const DEFAULT_TIMEOUT = 30;
    private const DEFAULT_CONNECT_TIMEOUT = 10;
    private const DEFAULT_RETRY_ATTEMPTS = 3;

    private string $apiKey;
    private string $baseUrl;
    private int $timeout;
    private int $connectTimeout;
    private int $retryAttempts;
    private bool $verifySSL;
    private ?string $userAgent;

    /** @var array<string, string> */
    private array $customHeaders;

    /**
     * Constructor
     *
     * @param string $apiKey API key for authentication
     * @param array<string, mixed> $options Configuration options
     * @throws ConfigurationException
     */
    public function __construct(string $apiKey, array $options = [])
    {
        if (empty($apiKey)) {
            throw new ConfigurationException('API key cannot be empty');
        }

        $this->apiKey = $apiKey;
        $this->baseUrl = $options['base_url'] ?? self::DEFAULT_BASE_URL;
        $this->timeout = $options['timeout'] ?? self::DEFAULT_TIMEOUT;
        $this->connectTimeout = $options['connect_timeout'] ?? self::DEFAULT_CONNECT_TIMEOUT;
        $this->retryAttempts = $options['retry_attempts'] ?? self::DEFAULT_RETRY_ATTEMPTS;
        $this->verifySSL = $options['verify_ssl'] ?? true;
        $this->userAgent = $options['user_agent'] ?? null;
        $this->customHeaders = $options['custom_headers'] ?? [];

        $this->validate();
    }

    /**
     * Create config from environment variables
     *
     * @param array<string, mixed> $options Additional options
     * @return self
     * @throws ConfigurationException
     */
    public static function fromEnvironment(array $options = []): self
    {
        $apiKey = getenv('DEVSLY_API_KEY');

        if ($apiKey === false || empty($apiKey)) {
            throw new ConfigurationException(
                'DEVSLY_API_KEY environment variable is not set'
            );
        }

        return new self($apiKey, $options);
    }

    /**
     * Validate configuration
     *
     * @throws ConfigurationException
     */
    private function validate(): void
    {
        if ($this->timeout < 1) {
            throw new ConfigurationException('Timeout must be greater than 0');
        }

        if ($this->connectTimeout < 1) {
            throw new ConfigurationException('Connect timeout must be greater than 0');
        }

        if ($this->retryAttempts < 0) {
            throw new ConfigurationException('Retry attempts cannot be negative');
        }

        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw new ConfigurationException('Invalid base URL provided');
        }
    }

    /**
     * Get API key
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Get base URL
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return rtrim($this->baseUrl, '/');
    }

    /**
     * Get timeout in seconds
     *
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Get connect timeout in seconds
     *
     * @return int
     */
    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    /**
     * Get retry attempts
     *
     * @return int
     */
    public function getRetryAttempts(): int
    {
        return $this->retryAttempts;
    }

    /**
     * Check if SSL verification is enabled
     *
     * @return bool
     */
    public function shouldVerifySSL(): bool
    {
        return $this->verifySSL;
    }

    /**
     * Get user agent string
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent ?? $this->getDefaultUserAgent();
    }

    /**
     * Get custom headers
     *
     * @return array<string, string>
     */
    public function getCustomHeaders(): array
    {
        return $this->customHeaders;
    }

    /**
     * Get default user agent
     *
     * @return string
     */
    private function getDefaultUserAgent(): string
    {
        $phpVersion = PHP_VERSION;
        $sdkVersion = '1.0.0';

        return sprintf('DevSLY-PHP-SDK/%s (PHP %s)', $sdkVersion, $phpVersion);
    }

    /**
     * Get all headers for API requests
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        $headers = [
            'X-API-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => $this->getUserAgent(),
        ];

        return array_merge($headers, $this->customHeaders);
    }
}
