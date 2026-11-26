<?php

declare(strict_types=1);

namespace App\Modules\Settings\Models;

use App\Shared\Audit\Auditable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property string $group
 * @property string|null $description
 */
class Setting extends Model
{
    use Auditable;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];
}
