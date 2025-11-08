<?php

declare(strict_types=1);

namespace DevSly\Contracts;

/**
 * Service Interface
 *
 * Base interface for all API service implementations
 */
interface ServiceInterface
{
    /**
     * Get the HTTP client instance
     *
     * @return HttpClientInterface
     */
    public function getHttpClient(): HttpClientInterface;
}
