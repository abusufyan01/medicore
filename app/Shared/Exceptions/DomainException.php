<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use RuntimeException;

class DomainException extends RuntimeException
{
    /**
     * @param  array<string, array<int, string>>  $fields
     */
    public function __construct(
        public readonly ErrorCode $errorCode,
        ?string $message = null,
        public readonly array $fields = [],
    ) {
        parent::__construct($message ?? $errorCode->defaultMessage());
    }

    public function code(): string
    {
        return $this->errorCode->value;
    }

    public function httpStatus(): int
    {
        return $this->errorCode->status();
    }
}
