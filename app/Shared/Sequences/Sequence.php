<?php

declare(strict_types=1);

namespace App\Shared\Sequences;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $prefix
 * @property int $current_value
 * @property int $padding
 * @property bool $include_year
 * @property int|null $last_year
 */
class Sequence extends Model
{
    protected $fillable = [
        'name',
        'prefix',
        'current_value',
        'padding',
        'include_year',
        'last_year',
    ];

    protected function casts(): array
    {
        return [
            'current_value' => 'integer',
            'padding' => 'integer',
            'include_year' => 'boolean',
            'last_year' => 'integer',
        ];
    }
}
