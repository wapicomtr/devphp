<?php

declare(strict_types=1);

namespace DevSly\Http;

use DevSly\Config;
use DevSly\Contracts\HttpClientInterface;
use DevSly\Exceptions\ApiException;
use DevSly\Exceptions\AuthenticationException;
use DevSly\Exceptions\NetworkException;
use DevSly\Exceptions\RateLimitException;
use DevSly\Exceptions\ValidationException;

/**
 * HTTP Client
 *
 * Handles all HTTP communication with the DevSLY API
 */
final class HttpClient implements HttpClientInterface
{
    private Config $config;
    private ?resource $curlHandle = null;

    /**
     * Constructor
     *
     * @param Config $config Configuration instance
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Destructor - cleanup curl handle
     */
    public function __destruct()
    {
        if ($this->curlHandle !== null) {
            curl_close($this->curlHandle);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $endpoint, array $queryParams = []): array
    {
        $url = $this->buildUrl($endpoint, $queryParams);
        return $this->request('GET', $url);
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $endpoint, array $data = []): array
    {
        $url = $this->buildUrl($endpoint);
        return $this->request('POST', $url, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $endpoint, array $data = []): array
    {
        $url = $this->buildUrl($endpoint);
        return $this->request('PUT', $url, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $endpoint, array $queryParams = []): array
    {
        $url = $this->buildUrl($endpoint, $queryParams);
        return $this->request('DELETE', $url);
    }

    /**
     * Build full URL with query parameters
     *
     * @param string $endpoint API endpoint
     * @param array<string, mixed> $queryParams Query parameters
     * @return string
     */
    private function buildUrl(string $endpoint, array $queryParams = []): string
    {
        $url = $this->config->getBaseUrl() . '/' . ltrim($endpoint, '/');

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * Execute HTTP request with retry logic
     *
     * @param string $method HTTP method
     * @param string $url Full URL
     * @param array<string, mixed>|null $data Request body data
     * @return array<string, mixed>
     * @throws ApiException
     */
    private function request(string $method, string $url, ?array $data = null): array
    {
        $attempts = 0;
        $maxAttempts = $this->config->getRetryAttempts() + 1;
        $lastException = null;

        while ($attempts < $maxAttempts) {
            try {
                return $this->executeRequest($method, $url, $data);
            } catch (NetworkException $e) {
                $lastException = $e;
                $attempts++;

                if ($attempts < $maxAttempts) {
                    // Exponential backoff: 1s, 2s, 4s
                    $delay = pow(2, $attempts - 1);
                    sleep($delay);
                    continue;
                }
            }
        }

        throw $lastException ?? new NetworkException('Request failed after all retry attempts');
    }

    /**
     * Execute a single HTTP request
     *
     * @param string $method HTTP method
     * @param string $url Full URL
     * @param array<string, mixed>|null $data Request body data
     * @return array<string, mixed>
     * @throws ApiException
     */
    private function executeRequest(string $method, string $url, ?array $data = null): array
    {
        $ch = curl_init();

        // Set basic options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => $this->config->getTimeout(),
            CURLOPT_CONNECTTIMEOUT => $this->config->getConnectTimeout(),
            CURLOPT_SSL_VERIFYPEER => $this->config->shouldVerifySSL(),
            CURLOPT_SSL_VERIFYHOST => $this->config->shouldVerifySSL() ? 2 : 0,
            CURLOPT_CUSTOMREQUEST => $method,
        ]);

        // Set headers
        $headers = [];
        foreach ($this->config->getHeaders() as $key => $value) {
            $headers[] = sprintf('%s: %s', $key, $value);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Set request body for POST/PUT
        if (in_array($method, ['POST', 'PUT']) && $data !== null) {
            $jsonData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        }

        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);

        curl_close($ch);

        // Handle cURL errors
        if ($curlErrno !== 0) {
            throw new NetworkException(
                sprintf('Network error: %s (Code: %d)', $curlError, $curlErrno),
                $curlErrno
            );
        }

        // Parse response
        if ($response === false) {
            throw new NetworkException('Empty response received from server');
        }

        return $this->handleResponse($response, $httpCode);
    }

    /**
     * Handle and parse API response
     *
     * @param string $response Raw response body
     * @param int $httpCode HTTP status code
     * @return array<string, mixed>
     * @throws ApiException
     */
    private function handleResponse(string $response, int $httpCode): array
    {
        // Parse JSON response
        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException(
                sprintf('Invalid JSON response: %s', json_last_error_msg()),
                $httpCode
            );
        }

        // Handle successful responses
        if ($httpCode >= 200 && $httpCode < 300) {
            return $data ?? [];
        }

        // Handle error responses
        $this->handleErrorResponse($data, $httpCode);

        // Fallback error
        throw new ApiException(
            $data['message'] ?? 'Unknown API error occurred',
            $httpCode
        );
    }

    /**
     * Handle error responses and throw appropriate exceptions
     *
     * @param array<string, mixed>|null $data Response data
     * @param int $httpCode HTTP status code
     * @throws ApiException
     */
    private function handleErrorResponse(?array $data, int $httpCode): void
    {
        $message = $data['message'] ?? 'Unknown error';
        $errorCode = $data['error'] ?? 'UNKNOWN_ERROR';

        // Rate limit exceeded
        if ($httpCode === 429 || $errorCode === 'RATE_LIMIT_EXCEEDED') {
            throw new RateLimitException($message, $httpCode);
        }

        // Authentication errors
        if ($httpCode === 401 || $errorCode === 'UNAUTHORIZED') {
            throw new AuthenticationException($message, $httpCode);
        }

        // Validation errors
        if ($httpCode === 400 || $errorCode === 'BAD_REQUEST' || $errorCode === 'VALIDATION_ERROR') {
            throw new ValidationException($message, $httpCode);
        }

        // Not found
        if ($httpCode === 404) {
            throw new ApiException('Resource not found: ' . $message, $httpCode);
        }

        // Server errors
        if ($httpCode >= 500) {
            throw new ApiException(
                'Server error: ' . $message,
                $httpCode
            );
        }
    }
}
