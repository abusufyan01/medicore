<?php

declare(strict_types=1);

namespace App\Modules\Patients\Services;

use App\Modules\Auth\Models\User;
use App\Modules\Patients\Models\Patient;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use App\Shared\Sequences\SequenceGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PatientService
{
    public function __construct(
        private readonly SequenceGenerator $sequenceGenerator,
    ) {}

    /**
     * @param  array{q?: string, is_active?: bool|string, per_page?: int|string}  $filters
     * @return LengthAwarePaginator<int, Patient>
     */
    public function paginateForUser(User $actor, array $filters): LengthAwarePaginator
    {
        $perPage = min(max((int) ($filters['per_page'] ?? 15), 1), 100);

        $query = Patient::query();

        // Role scoping: Admin & Receptionist see all; Patient sees own; Doctor stubbed for assigned-only.
        if (! $actor->hasRole('admin') && ! $actor->hasRole('receptionist')) {
            if ($actor->hasRole('doctor')) {
                // Stub: will filter to patients assigned via appointments/admissions in Phases 3 & 7.
                $query->whereRaw('1 = 0');
            } elseif ($actor->hasRole('patient')) {
                $query->where('user_id', $actor->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $query->when(
            isset($filters['q']) && trim((string) $filters['q']) !== '',
            function (Builder $q) use ($filters): void {
                $term = '%'.trim((string) $filters['q']).'%';
                $q->where(function (Builder $sub) use ($term): void {
                    $sub->where('patient_number', 'like', $term)
                        ->orWhere('name', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('national_id', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            }
        )->when(
            array_key_exists('is_active', $filters) && $filters['is_active'] !== '',
            function (Builder $q) use ($filters): void {
                $active = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($active !== null) {
                    $q->where('is_active', $active);
                }
            }
        );

        return $query->orderByDesc('id')->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Patient
    {
        $data = $this->normalizeAttributes($data);
        $confirmDuplicate = filter_var($data['confirm_duplicate'] ?? false, FILTER_VALIDATE_BOOLEAN);
        unset($data['confirm_duplicate']);

        if (! $confirmDuplicate) {
            $this->ensureNoDuplicate($data);
        }

        return DB::transaction(function () use ($data): Patient {
            $patient = new Patient($data);
            $patient->patient_number = $this->sequenceGenerator->next('patient');
            $patient->is_active = true;
            $patient->save();

            return $patient;
        });
    }

    /**
     * Check for potential duplicates matching (phone + date_of_birth) OR (name + date_of_birth).
     *
     * @param  array<string, mixed>  $data
     */
    private function ensureNoDuplicate(array $data): void
    {
        $name = mb_strtolower(trim((string) ($data['name'] ?? '')));
        $phone = trim((string) ($data['phone'] ?? ''));
        $dob = (string) ($data['date_of_birth'] ?? '');

        $duplicates = Patient::query()
            ->whereDate('date_of_birth', $dob)
            ->where(function (Builder $query) use ($phone, $name): void {
                $query->where('phone', $phone)
                    ->orWhereRaw('LOWER(TRIM(name)) = ?', [$name]);
            })
            ->get(['id', 'patient_number', 'name', 'phone', 'date_of_birth']);

        if ($duplicates->isNotEmpty()) {
            $matches = $duplicates->map(
                fn (Patient $p): string => "{$p->patient_number} ({$p->name}, {$p->phone})"
            )->all();

            throw new DomainException(
                ErrorCode::DuplicatePatient,
                'A potential duplicate patient record already exists. Pass confirm_duplicate=true to create anyway.',
                ['duplicates' => $matches]
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Patient $patient, array $data): Patient
    {
        $patient->fill($this->normalizeAttributes($data));
        $patient->fill($data);
        $patient->save();

        return $patient;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeAttributes(array $data): array
    {
        foreach (['name', 'phone', 'national_id', 'guardian_name', 'guardian_phone'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $trimmed = trim($data[$field]);
                $data[$field] = $trimmed === '' ? null : $trimmed;
            }
        }

        if (isset($data['email']) && is_string($data['email'])) {
            $trimmedEmail = strtolower(trim($data['email']));
            $data['email'] = $trimmedEmail === '' ? null : $trimmedEmail;
        }

        return $data;
    }

    public function updateStatus(Patient $patient, bool $isActive): Patient
    {
        $patient->is_active = $isActive;
        $patient->save();

        return $patient;
    }
}
