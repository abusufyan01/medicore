<?php

declare(strict_types=1);

namespace App\Modules\Patients\Models;

use App\Modules\Auth\Models\User;
use App\Shared\Audit\Auditable;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $patient_number
 * @property int|null $user_id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property Carbon $date_of_birth
 * @property string $gender
 * @property string|null $national_id
 * @property string|null $blood_group
 * @property string|null $address
 * @property string|null $guardian_name
 * @property string|null $guardian_phone
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_phone
 * @property string|null $allergies
 * @property string|null $chronic_conditions
 * @property string|null $notes
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Patient extends Model
{
    use Auditable, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'patient_number',
        'user_id',
        'name',
        'phone',
        'email',
        'date_of_birth',
        'gender',
        'national_id',
        'blood_group',
        'address',
        'guardian_name',
        'guardian_phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'allergies',
        'chronic_conditions',
        'notes',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::forceDeleting(function (): void {
            throw new DomainException(
                ErrorCode::Forbidden,
                'Patient records cannot be permanently deleted. Deactivate the patient instead.'
            );
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
