<?php

declare(strict_types=1);

namespace App\Modules\Doctors\Http\Controllers;

use App\Modules\Doctors\Http\Requests\StoreSpecializationRequest;
use App\Modules\Doctors\Http\Requests\UpdateSpecializationRequest;
use App\Modules\Doctors\Http\Resources\SpecializationResource;
use App\Modules\Doctors\Models\Specialization;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpecializationController
{
    use AuthorizesRequests;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Specialization::class);

        $perPage = min(max((int) $request->input('per_page', 50), 1), 100);

        $paginator = Specialization::query()
            ->with('department')
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = '%'.trim((string) $request->input('q')).'%';
                $query->where('name', 'like', $term);
            })
            ->when($request->filled('department_id'), fn (Builder $query) => $query->where('department_id', (int) $request->input('department_id')))
            ->when($request->has('is_active') && $request->input('is_active') !== '', function (Builder $query) use ($request): void {
                $active = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($active !== null) {
                    $query->where('is_active', $active);
                }
            })
            ->orderBy('name')
            ->paginate($perPage);

        $items = collect($paginator->items())
            ->map(fn (Specialization $spec): array => (new SpecializationResource($spec))->resolve())
            ->all();

        return ApiResponse::paginated($paginator, SpecializationResource::class);
    }

    public function store(StoreSpecializationRequest $request): JsonResponse
    {
        $this->authorize('create', Specialization::class);

        /** @var array{department_id?: int|null, name: string, description?: string|null, is_active?: bool} $validated */
        $validated = $request->validated();
        $validated['name'] = trim($validated['name']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $specialization = Specialization::create($validated);

        return ApiResponse::created(new SpecializationResource($specialization->load('department')));
    }

    public function show(Specialization $specialization): JsonResponse
    {
        $this->authorize('view', $specialization);

        return ApiResponse::success(new SpecializationResource($specialization->load('department')));
    }

    public function update(UpdateSpecializationRequest $request, Specialization $specialization): JsonResponse
    {
        $this->authorize('update', $specialization);

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        if (isset($validated['name']) && is_string($validated['name'])) {
            $validated['name'] = trim($validated['name']);
        }

        $specialization->fill($validated);
        $specialization->save();

        return ApiResponse::success(new SpecializationResource($specialization->load('department')));
    }

    public function destroy(Specialization $specialization): Response
    {
        $this->authorize('delete', $specialization);

        $specialization->delete();

        return ApiResponse::noContent();
    }
}
