<?php

declare(strict_types=1);

namespace App\Shared\Sequences;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class SequenceGenerator
{
    /**
     * Atomically increment and format the next sequence number under a row-level lock.
     */
    public function next(string $name): string
    {
        return DB::transaction(function () use ($name): string {
            /** @var Sequence|null $sequence */
            $sequence = Sequence::where('name', $name)
                ->lockForUpdate()
                ->first();

            if ($sequence === null) {
                throw new RuntimeException("Sequence [{$name}] is not configured.");
            }

            $currentYear = (int) now()->utc()->format('Y');

            if ($sequence->include_year && $sequence->last_year !== $currentYear) {
                $sequence->current_value = 0;
                $sequence->last_year = $currentYear;
            }

            $sequence->current_value++;
            $sequence->save();

            $padded = str_pad((string) $sequence->current_value, $sequence->padding, '0', STR_PAD_LEFT);

            if ($sequence->include_year) {
                return sprintf('%s-%d-%s', $sequence->prefix, $currentYear, $padded);
            }

            return sprintf('%s-%s', $sequence->prefix, $padded);
        });
    }
}
