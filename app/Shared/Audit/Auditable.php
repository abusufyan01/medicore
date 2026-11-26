<?php

declare(strict_types=1);

namespace App\Shared\Audit;

use Illuminate\Support\Arr;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (self $model): void {
            app(AuditLogger::class)->log(
                event: 'created',
                auditable: $model,
                newValues: $model->filterAuditableAttributes($model->getAttributes()),
            );
        });

        static::updated(function (self $model): void {
            $dirty = $model->filterAuditableAttributes($model->getChanges());

            if ($dirty === []) {
                return;
            }

            /** @var array<string, mixed> $original */
            $original = Arr::only($model->getOriginal(), array_keys($dirty));

            app(AuditLogger::class)->log(
                event: 'updated',
                auditable: $model,
                oldValues: $original,
                newValues: $dirty,
            );
        });

        static::deleted(function (self $model): void {
            app(AuditLogger::class)->log(
                event: 'deleted',
                auditable: $model,
                oldValues: $model->filterAuditableAttributes($model->getOriginal()),
            );
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function filterAuditableAttributes(array $attributes): array
    {
        $ignored = ['updated_at', 'created_at', 'remember_token'];

        return Arr::except($attributes, $ignored);
    }
}
