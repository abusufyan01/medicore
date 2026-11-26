<?php

declare(strict_types=1);

namespace App\Modules\Settings\Services;

use App\Modules\Settings\Models\Setting;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    private const CACHE_KEY = 'hms:settings:all';

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        /** @var array<string, mixed> $cached */
        $cached = Cache::rememberForever(self::CACHE_KEY, function (): array {
            $result = [];

            foreach (Setting::orderBy('group')->orderBy('key')->get() as $setting) {
                $result[$setting->key] = [
                    'value' => $this->castValue($setting->value, $setting->type),
                    'type' => $setting->type,
                    'group' => $setting->group,
                    'description' => $setting->description,
                ];
            }

            return $result;
        });

        return $cached;
    }

    public function getString(string $key, string $default = ''): string
    {
        $entry = $this->all()[$key] ?? null;

        return $entry !== null ? (string) $entry['value'] : $default;
    }

    public function getInt(string $key, int $default = 0): int
    {
        $entry = $this->all()[$key] ?? null;

        return $entry !== null ? (int) $entry['value'] : $default;
    }

    public function getFloat(string $key, float $default = 0.0): float
    {
        $entry = $this->all()[$key] ?? null;

        return $entry !== null ? (float) $entry['value'] : $default;
    }

    public function getBool(string $key, bool $default = false): bool
    {
        $entry = $this->all()[$key] ?? null;

        return $entry !== null ? (bool) $entry['value'] : $default;
    }

    /**
     * @param  array<string, mixed>  $updates
     * @return array<string, mixed>
     */
    public function updateMany(array $updates): array
    {
        DB::transaction(function () use ($updates): void {
            foreach ($updates as $key => $rawValue) {
                /** @var Setting|null $setting */
                $setting = Setting::where('key', $key)->first();

                if ($setting === null) {
                    throw new DomainException(
                        ErrorCode::ValidationFailed,
                        "Unknown setting key [{$key}].",
                        ['settings' => ["Unknown setting key [{$key}]."]]
                    );
                }

                $setting->value = $this->serializeValue($rawValue, $setting->type);
                $setting->save();
            }
        });

        Cache::forget(self::CACHE_KEY);

        return $this->all();
    }

    private function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    private function serializeValue(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0',
            'json' => is_string($value) ? $value : (string) json_encode($value),
            default => is_scalar($value) ? (string) $value : '',
        };
    }
}
