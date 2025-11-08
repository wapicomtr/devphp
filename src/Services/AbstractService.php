<?php

declare(strict_types=1);

namespace DevSly\Services;

use DevSly\Contracts\HttpClientInterface;
use DevSly\Contracts\ServiceInterface;

/**
 * Abstract Service
 *
 * Base class for all API services
 */
abstract class AbstractService implements ServiceInterface
{
    protected HttpClientInterface $httpClient;

    /**
     * Constructor
     *
     * @param HttpClientInterface $httpClient HTTP client instance
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }
}
