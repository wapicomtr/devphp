<?php

declare(strict_types=1);

namespace DevSly\Contracts;

/**
 * HTTP Client Interface
 *
 * Defines the contract for HTTP client implementations
 */
interface HttpClientInterface
{
    /**
     * Send a GET request
     *
     * @param string $endpoint API endpoint
     * @param array<string, mixed> $queryParams Query parameters
     * @return array<string, mixed> Response data
     */
    public function get(string $endpoint, array $queryParams = []): array;

    /**
     * Send a POST request
     *
     * @param string $endpoint API endpoint
     * @param array<string, mixed> $data Request body data
     * @return array<string, mixed> Response data
     */
    public function post(string $endpoint, array $data = []): array;

    /**
     * Send a PUT request
     *
     * @param string $endpoint API endpoint
     * @param array<string, mixed> $data Request body data
     * @return array<string, mixed> Response data
     */
    public function put(string $endpoint, array $data = []): array;

    /**
     * Send a DELETE request
     *
     * @param string $endpoint API endpoint
     * @param array<string, mixed> $queryParams Query parameters
     * @return array<string, mixed> Response data
     */
    public function delete(string $endpoint, array $queryParams = []): array;
}
