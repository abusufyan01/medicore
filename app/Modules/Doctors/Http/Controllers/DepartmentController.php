<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Http\Controllers;

use App\Modules\Doctors\Http\Requests\StoreDepartmentRequest;
use App\Modules\Doctors\Http\Requests\UpdateDepartmentRequest;
use App\Modules\Doctors\Http\Resources\DepartmentResource;
use App\Modules\Doctors\Models\Department;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DepartmentController
{
    use AuthorizesRequests;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Department::class);

        $perPage = min(max((int) $request->input('per_page', 50), 1), 100);

        $paginator = Department::query()
            ->withCount('specializations')
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = '%'.trim((string) $request->input('q')).'%';
                $query->where(fn (Builder $q) => $q->where('name', 'like', $term)->orWhere('code', 'like', $term));
            })
            ->when($request->has('is_active') && $request->input('is_active') !== '', function (Builder $query) use ($request): void {
                $active = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($active !== null) {
                    $query->where('is_active', $active);
                }
            })
            ->orderBy('name')
            ->paginate($perPage);

        $items = collect($paginator->items())
            ->map(fn (Department $dept): array => (new DepartmentResource($dept))->resolve())
            ->all();

        return ApiResponse::paginated($paginator, DepartmentResource::class);
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $this->authorize('create', Department::class);

        /** @var array{code: string, name: string, description?: string|null, is_active?: bool} $validated */
        $validated = $request->validated();
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['name'] = trim($validated['name']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $department = Department::create($validated);

        return ApiResponse::created(new DepartmentResource($department));
    }

    public function show(Department $department): JsonResponse
    {
        $this->authorize('view', $department);

        return ApiResponse::success(new DepartmentResource($department->loadCount('specializations')));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): JsonResponse
    {
        $this->authorize('update', $department);

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        if (isset($validated['code']) && is_string($validated['code'])) {
            $validated['code'] = strtoupper(trim($validated['code']));
        }
        if (isset($validated['name']) && is_string($validated['name'])) {
            $validated['name'] = trim($validated['name']);
        }

        $department->fill($validated);
        $department->save();

        return ApiResponse::success(new DepartmentResource($department));
    }

    public function destroy(Department $department): Response
    {
        $this->authorize('delete', $department);

        $department->delete();

        return ApiResponse::noContent();
    }
}
