<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Services;

use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Notifications\UserAccountCreatedNotification;
use App\Modules\Doctors\Models\Doctor;
use App\Modules\Settings\Services\SettingsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DoctorService
{
    public function __construct(
        private readonly SettingsService $settingsService,
    ) {}

    /**
     * @param  array{q?: string, name?: string, specialization_id?: int|string, department_id?: int|string, is_active?: bool|string, per_page?: int|string}  $filters
     * @return LengthAwarePaginator<int, Doctor>
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $perPage = min(max((int) ($filters['per_page'] ?? 15), 1), 100);

        $searchTerm = trim((string) ($filters['q'] ?? $filters['name'] ?? ''));

        return Doctor::query()
            ->with(['user', 'department', 'specialization'])
            ->when($searchTerm !== '', function (Builder $query) use ($searchTerm): void {
                $like = '%'.$searchTerm.'%';
                $query->where(function (Builder $sub) use ($like): void {
                    $sub->where('license_number', 'like', $like)
                        ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', $like));
                });
            })
            ->when(
                isset($filters['specialization_id']) && $filters['specialization_id'] !== '',
                fn (Builder $query) => $query->where('specialization_id', (int) $filters['specialization_id'])
            )
            ->when(
                isset($filters['department_id']) && $filters['department_id'] !== '',
                fn (Builder $query) => $query->where('department_id', (int) $filters['department_id'])
            )
            ->when(
                array_key_exists('is_active', $filters) && $filters['is_active'] !== '',
                function (Builder $query) use ($filters): void {
                    $active = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($active !== null) {
                        $query->where('is_active', $active);
                    }
                }
            )
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Atomically create User account, assign 'doctor' role, and create Doctor profile.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Doctor
    {
        return DB::transaction(function () use ($data): Doctor {
            $temporaryPassword = Str::password(12, true, true, false, false);

            $user = new User;
            $user->name = trim((string) $data['name']);
            $user->email = strtolower(trim((string) $data['email']));
            $user->phone = trim((string) $data['phone']);
            $user->password = $temporaryPassword;
            $user->email_verified_at = now();
            $user->is_active = true;
            $user->must_change_password = true;
            $user->save();

            $doctorRole = Role::where('name', 'doctor')->firstOrFail();
            $user->roles()->attach($doctorRole->id);

            $defaultSlot = $this->settingsService->getInt('appointments.slot_duration_minutes', 15);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'department_id' => (int) $data['department_id'],
                'specialization_id' => (int) $data['specialization_id'],
                'license_number' => strtoupper(trim((string) $data['license_number'])),
                'qualification' => isset($data['qualification']) ? trim((string) $data['qualification']) : null,
                'experience_years' => (int) ($data['experience_years'] ?? 0),
                'consultation_fee' => number_format((float) $data['consultation_fee'], 2, '.', ''),
                'slot_duration_minutes' => (int) ($data['slot_duration_minutes'] ?? $defaultSlot),
                'bio' => isset($data['bio']) ? trim((string) $data['bio']) : null,
                'is_active' => true,
            ]);

            $user->notify(new UserAccountCreatedNotification($temporaryPassword));

            return $doctor->load(['user', 'department', 'specialization']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Doctor $doctor, array $data): Doctor
    {
        return DB::transaction(function () use ($doctor, $data): Doctor {
            $userFields = Arr::only($data, ['name', 'phone']);
            if ($userFields !== []) {
                if (isset($userFields['name']) && is_string($userFields['name'])) {
                    $userFields['name'] = trim($userFields['name']);
                }
                if (isset($userFields['phone']) && is_string($userFields['phone'])) {
                    $userFields['phone'] = trim($userFields['phone']);
                }
                $doctor->user->fill($userFields);
                $doctor->user->save();
            }

            $doctorFields = Arr::except($data, ['name', 'phone']);
            if (isset($doctorFields['license_number']) && is_string($doctorFields['license_number'])) {
                $doctorFields['license_number'] = strtoupper(trim($doctorFields['license_number']));
            }
            if (isset($doctorFields['consultation_fee'])) {
                $doctorFields['consultation_fee'] = number_format((float) $doctorFields['consultation_fee'], 2, '.', '');
            }

            $doctor->fill($doctorFields);
            $doctor->save();

            return $doctor->load(['user', 'department', 'specialization']);
        });
    }

    public function updateStatus(Doctor $doctor, bool $isActive): Doctor
    {
        return DB::transaction(function () use ($doctor, $isActive): Doctor {
            /** @var Doctor $lockedDoctor */
            $lockedDoctor = Doctor::whereKey($doctor->id)->lockForUpdate()->firstOrFail();

            if (! $isActive && $lockedDoctor->is_active) {
                $this->ensureNoFutureAppointments($lockedDoctor);
            }

            $lockedDoctor->is_active = $isActive;
            $lockedDoctor->save();

            $lockedDoctor->user->is_active = $isActive;
            $lockedDoctor->user->save();

            return $lockedDoctor->load(['user', 'department', 'specialization']);
        });
    }

    /**
     * Stub: will block deactivation with 409 INVALID_STATE when upcoming appointments exist (Phase 5).
     */
    private function ensureNoFutureAppointments(Doctor $doctor): void
    {
        // Implemented in Phase 5 once the appointments table is created.
    }
}
