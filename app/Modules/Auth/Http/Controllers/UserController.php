<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers;

use App\Modules\Auth\Http\Requests\StoreUserRequest;
use App\Modules\Auth\Http\Requests\SyncUserRolesRequest;
use App\Modules\Auth\Http\Requests\UpdateUserRequest;
use App\Modules\Auth\Http\Requests\UpdateUserStatusRequest;
use App\Modules\Auth\Http\Resources\UserResource;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Services\UserAdministrationService;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController
{
    public function __construct(
        private readonly UserAdministrationService $userService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        /** @var array{search?: string, role?: string, is_active?: bool|string, per_page?: int|string} $filters */
        $filters = $request->only(['search', 'role', 'is_active', 'per_page']);
        $paginator = $this->userService->paginate($filters);

        return ApiResponse::paginated($paginator, UserResource::class);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        /** @var array{name: string, email: string, phone?: string|null, roles: list<string>} $validated */
        $validated = $request->validated();

        $user = $this->userService->create($validated);

        return ApiResponse::created(new UserResource($user));
    }

    public function show(User $user): JsonResponse
    {
        return ApiResponse::success(new UserResource($user->load('roles')));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        /** @var array{name?: string, email?: string, phone?: string|null} $validated */
        $validated = $request->validated();

        $updated = $this->userService->update($user, $validated);

        return ApiResponse::success(new UserResource($updated));
    }

    public function updateStatus(UpdateUserStatusRequest $request, User $user): JsonResponse
    {
        /** @var bool $isActive */
        $isActive = $request->validated('is_active');

        $updated = $this->userService->updateStatus($user, $isActive);

        return ApiResponse::success(new UserResource($updated));
    }

    public function syncRoles(SyncUserRolesRequest $request, User $user): JsonResponse
    {
        /** @var list<string> $roles */
        $roles = $request->validated('roles');

        $updated = $this->userService->syncRoles($user, $roles);

        return ApiResponse::success(new UserResource($updated));
    }
}
