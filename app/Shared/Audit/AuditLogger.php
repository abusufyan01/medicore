<?php

declare(strict_types=1);

namespace App\Shared\Audit;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /** @var list<string> */
    private const REDACTED_KEYS = [
        'password',
        'password_confirmation',
        'remember_token',
        'token',
    ];

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function log(
        string $event,
        Model|string $auditable,
        int|string|null $auditableId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null,
    ): AuditLog {
        $type = $auditable instanceof Model ? $auditable->getMorphClass() : $auditable;
        $id = $auditable instanceof Model ? (string) $auditable->getKey() : ($auditableId !== null ? (string) $auditableId : null);

        $actorId = $userId;
        if ($actorId === null && Auth::guard('sanctum')->check()) {
            /** @var User|null $authUser */
            $authUser = Auth::guard('sanctum')->user();
            $actorId = $authUser?->id;
        }
        if ($actorId === null && Auth::check()) {
            $actorId = (int) Auth::id();
        }

        $requestId = Request::header('X-Request-Id');

        return AuditLog::create([
            'user_id' => $actorId,
            'event' => $event,
            'auditable_type' => $type,
            'auditable_id' => $id,
            'old_values' => $this->redact($oldValues),
            'new_values' => $this->redact($newValues),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'request_id' => is_string($requestId) && $requestId !== '' ? $requestId : null,
            'created_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $values
     * @return array<string, mixed>|null
     */
    private function redact(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        foreach ($values as $key => $value) {
            if (in_array(strtolower((string) $key), self::REDACTED_KEYS, true)) {
                $values[$key] = '[REDACTED]';
            }
        }

        return $values;
    }
}
