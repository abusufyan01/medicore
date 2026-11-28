<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Http\Controllers;

use App\Modules\Doctors\Http\Requests\StoreDoctorRequest;
use App\Modules\Doctors\Http\Requests\UpdateDoctorRequest;
use App\Modules\Doctors\Http\Requests\UpdateDoctorStatusRequest;
use App\Modules\Doctors\Http\Resources\DoctorResource;
use App\Modules\Doctors\Models\Doctor;
use App\Modules\Doctors\Services\DoctorService;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController
{
    use AuthorizesRequests;

    public function __construct(
        private readonly DoctorService $doctorService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Doctor::class);

        /** @var array{q?: string, name?: string, specialization_id?: int|string, department_id?: int|string, is_active?: bool|string, per_page?: int|string} $filters */
        $filters = $request->only(['q', 'name', 'specialization_id', 'department_id', 'is_active', 'per_page']);
        $paginator = $this->doctorService->paginate($filters);

        return ApiResponse::paginated($paginator, DoctorResource::class);
    }

    public function store(StoreDoctorRequest $request): JsonResponse
    {
        $this->authorize('create', Doctor::class);

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        $doctor = $this->doctorService->create($validated);

        return ApiResponse::created(new DoctorResource($doctor));
    }

    public function show(Doctor $doctor): JsonResponse
    {
        $this->authorize('view', $doctor);

        return ApiResponse::success(new DoctorResource($doctor));
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): JsonResponse
    {
        $this->authorize('update', $doctor);

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        $updated = $this->doctorService->update($doctor, $validated);

        return ApiResponse::success(new DoctorResource($updated));
    }

    public function updateStatus(UpdateDoctorStatusRequest $request, Doctor $doctor): JsonResponse
    {
        $this->authorize('updateStatus', $doctor);

        /** @var bool $isActive */
        $isActive = $request->validated('is_active');
        $updated = $this->doctorService->updateStatus($doctor, $isActive);

        return ApiResponse::success(new DoctorResource($updated));
    }
}
