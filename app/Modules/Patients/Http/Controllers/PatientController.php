<?php

declare(strict_types=1);

namespace App\Modules\Patients\Http\Controllers;

use App\Modules\Auth\Models\User;
use App\Modules\Patients\Http\Requests\InvitePatientPortalRequest;
use App\Modules\Patients\Http\Requests\LinkPatientAccountRequest;
use App\Modules\Patients\Http\Requests\StorePatientRequest;
use App\Modules\Patients\Http\Requests\UpdatePatientRequest;
use App\Modules\Patients\Http\Requests\UpdatePatientStatusRequest;
use App\Modules\Patients\Http\Resources\PatientResource;
use App\Modules\Patients\Models\Patient;
use App\Modules\Patients\Services\PatientPortalService;
use App\Modules\Patients\Services\PatientService;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController
{
    use AuthorizesRequests;

    public function __construct(
        private readonly PatientService $patientService,
        private readonly PatientPortalService $portalService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Patient::class);

        /** @var User $actor */
        $actor = $request->user();
        /** @var array{q?: string, is_active?: bool|string, per_page?: int|string} $filters */
        $filters = $request->only(['q', 'is_active', 'per_page']);

        $paginator = $this->patientService->paginateForUser($actor, $filters);

        return ApiResponse::paginated($paginator, PatientResource::class);
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        $this->authorize('create', Patient::class);

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        $patient = $this->patientService->create($validated);

        return ApiResponse::created(new PatientResource($patient));
    }

    public function show(Patient $patient): JsonResponse
    {
        $this->authorize('view', $patient);

        return ApiResponse::success(new PatientResource($patient));
    }

    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('update', $patient);

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        $updated = $this->patientService->update($patient, $validated);

        return ApiResponse::success(new PatientResource($updated));
    }

    public function updateStatus(UpdatePatientStatusRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('updateStatus', $patient);

        /** @var bool $isActive */
        $isActive = $request->validated('is_active');

        $updated = $this->patientService->updateStatus($patient, $isActive);

        return ApiResponse::success(new PatientResource($updated));
    }

    public function linkAccount(LinkPatientAccountRequest $request): JsonResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        /** @var string $patientNumber */
        $patientNumber = $request->validated('patient_number');
        /** @var string $dateOfBirth */
        $dateOfBirth = $request->validated('date_of_birth');

        $patient = $this->portalService->linkAccount($actor, $patientNumber, $dateOfBirth);

        return ApiResponse::success(new PatientResource($patient));
    }

    public function invite(InvitePatientPortalRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('update', $patient);

        /** @var User $actor */
        $actor = $request->user();
        /** @var string|null $email */
        $email = $request->validated('email');

        $updated = $this->portalService->invitePatient($patient, $email, $actor);

        return ApiResponse::created(new PatientResource($updated));
    }

    public function timeline(Patient $patient): JsonResponse
    {
        $this->authorize('view', $patient);

        return ApiResponse::success($this->portalService->timeline($patient));
    }
}
