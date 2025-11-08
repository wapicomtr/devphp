<?php

declare(strict_types=1);

namespace DevSly\Exceptions;

use Exception;

/**
 * Base DevSLY Exception
 *
 * All SDK exceptions extend this base class
 */
class DevSlyException extends Exception
{
    /**
     * Constructor
     *
     * @param string $message Error message
     * @param int $code Error code
     * @param Exception|null $previous Previous exception
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
