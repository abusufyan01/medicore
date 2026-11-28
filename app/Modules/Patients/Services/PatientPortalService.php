<?php

declare(strict_types=1);

namespace App\Modules\Patients\Services;

use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\Patients\Models\Patient;
use App\Modules\Patients\Notifications\PatientPortalInvitationNotification;
use App\Shared\Audit\AuditLogger;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PatientPortalService
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * Link an authenticated patient portal user to their clinical patient record.
     */
    public function linkAccount(User $user, string $patientNumber, string $dateOfBirth): Patient
    {
        if (! $user->hasRole('patient')) {
            throw new DomainException(
                ErrorCode::Forbidden,
                'Only patient portal accounts can self-link to a patient record.'
            );
        }

        return DB::transaction(function () use ($user, $patientNumber, $dateOfBirth): Patient {
            $alreadyLinkedToUser = Patient::where('user_id', $user->id)->exists();

            if ($alreadyLinkedToUser) {
                throw new DomainException(
                    ErrorCode::InvalidState,
                    'Your user account is already linked to a patient record.'
                );
            }

            $normalizedNumber = strtoupper(trim($patientNumber));

            /** @var Patient|null $patient */
            $patient = Patient::where('patient_number', $normalizedNumber)
                ->whereDate('date_of_birth', $dateOfBirth)
                ->lockForUpdate()
                ->first();

            if ($patient === null || ! $patient->is_active || $patient->user_id !== null) {
                throw new DomainException(
                    ErrorCode::ValidationFailed,
                    'Unable to link account. Verify your patient number and date of birth, or contact reception.',
                    ['patient_number' => ['No eligible unlinked patient record matched the provided details.']]
                );
            }

            $patient->user_id = $user->id;
            $patient->save();

            $this->auditLogger->log(
                event: 'patient_portal_linked',
                auditable: $patient,
                newValues: ['user_id' => $user->id],
                userId: $user->id,
            );

            return $patient;
        });
    }

    /**
     * Receptionist/Admin creates a portal user account for an unlinked patient and sends an invitation.
     */
    public function invitePatient(Patient $patient, ?string $overrideEmail, User $actor): Patient
    {
        return DB::transaction(function () use ($patient, $overrideEmail, $actor): Patient {
            /** @var Patient $lockedPatient */
            $lockedPatient = Patient::whereKey($patient->id)->lockForUpdate()->firstOrFail();

            if ($lockedPatient->user_id !== null) {
                throw new DomainException(
                    ErrorCode::InvalidState,
                    'This patient already has a linked portal account.'
                );
            }

            $email = $overrideEmail !== null && trim($overrideEmail) !== ''
                ? strtolower(trim($overrideEmail))
                : ($lockedPatient->email !== null ? strtolower(trim($lockedPatient->email)) : '');

            if ($email === '') {
                throw new DomainException(
                    ErrorCode::ValidationFailed,
                    'An email address is required to send a portal invitation.',
                    ['email' => ['Provide an email address because this patient has none on file.']]
                );
            }

            $validator = Validator::make(
                ['email' => $email],
                ['email' => ['required', 'email', 'unique:users,email']]
            );

            if ($validator->fails()) {
                throw new DomainException(
                    ErrorCode::ValidationFailed,
                    'A portal user account with this email address already exists.',
                    ['email' => ['This email address is already registered to a user account.']]
                );
            }

            $temporaryPassword = Str::password(12, true, true, false, false);

            $portalUser = new User;
            $portalUser->name = $lockedPatient->name;
            $portalUser->email = $email;
            $portalUser->phone = $lockedPatient->phone;
            $portalUser->password = $temporaryPassword;
            $portalUser->email_verified_at = now();
            $portalUser->is_active = true;
            $portalUser->must_change_password = true;
            $portalUser->save();

            $patientRole = Role::where('name', 'patient')->firstOrFail();
            $portalUser->roles()->attach($patientRole->id);

            $lockedPatient->user_id = $portalUser->id;
            if ($lockedPatient->email === null) {
                $lockedPatient->email = $email;
            }
            $lockedPatient->save();

            $portalUser->notify(new PatientPortalInvitationNotification($lockedPatient, $temporaryPassword));

            $this->auditLogger->log(
                event: 'patient_portal_invited',
                auditable: $lockedPatient,
                newValues: ['user_id' => $portalUser->id, 'email' => $email],
                userId: $actor->id,
            );

            return $lockedPatient;
        });
    }

    /**
     * Stubbed patient clinical timeline (populated in Phases 3, 4, 5, and 7).
     *
     * @return array<string, mixed>
     */
    public function timeline(Patient $patient): array
    {
        return [
            'patient_id' => $patient->id,
            'patient_number' => $patient->patient_number,
            'events' => [],
            'counts' => [
                'appointments' => 0,
                'consultations' => 0,
                'prescriptions' => 0,
                'invoices' => 0,
                'admissions' => 0,
            ],
        ];
    }
}
