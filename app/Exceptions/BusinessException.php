<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class BusinessException extends Exception
{
    public function __construct(
        string $message = 'A business rule was violated.',
        public readonly string $errorCode = 'BUSINESS_RULE_VIOLATION',
        private readonly int $httpStatus = 409,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function status(): int
    {
        return $this->httpStatus;
    }
}
