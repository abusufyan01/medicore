<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use RuntimeException;

class DomainException extends RuntimeException
{
    /**
     * @param  array<string, array<int, string>>|null  $fields
     */
    public function __construct(
        public readonly ErrorCode $errorCode,
        ?string $message = null,
        public readonly ?array $fields = null,
    ) {
        parent::__construct($message ?? $errorCode->defaultMessage());
    }
}
