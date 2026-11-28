<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Models;

use App\Modules\Auth\Models\User;
use App\Shared\Audit\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $department_id
 * @property int $specialization_id
 * @property string $license_number
 * @property string|null $qualification
 * @property int $experience_years
 * @property string $consultation_fee
 * @property int $slot_duration_minutes
 * @property string|null $bio
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Doctor extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'specialization_id',
        'license_number',
        'qualification',
        'experience_years',
        'consultation_fee',
        'slot_duration_minutes',
        'bio',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'department_id' => 'integer',
            'specialization_id' => 'integer',
            'experience_years' => 'integer',
            'consultation_fee' => 'decimal:2',
            'slot_duration_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo<Specialization, $this>
     */
    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
