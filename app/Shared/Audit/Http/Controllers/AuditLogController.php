<?php

declare(strict_types=1);

namespace App\Shared\Audit\Http\Controllers;

use App\Shared\Audit\AuditLog;
use App\Shared\Audit\Http\Resources\AuditLogResource;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);

        $paginator = AuditLog::query()
            ->with('user')
            ->when($request->filled('user_id'), fn (Builder $q) => $q->where('user_id', (int) $request->input('user_id')))
            ->when($request->filled('event'), fn (Builder $q) => $q->where('event', (string) $request->input('event')))
            ->when($request->filled('auditable_type'), fn (Builder $q) => $q->where('auditable_type', 'like', '%'.(string) $request->input('auditable_type').'%'))
            ->when($request->filled('auditable_id'), fn (Builder $q) => $q->where('auditable_id', (string) $request->input('auditable_id')))
            ->when($request->filled('from'), fn (Builder $q) => $q->whereDate('created_at', '>=', (string) $request->input('from')))
            ->when($request->filled('to'), fn (Builder $q) => $q->whereDate('created_at', '<=', (string) $request->input('to')))
            ->orderByDesc('id')
            ->paginate($perPage);

        $items = collect($paginator->items())
            ->map(fn (AuditLog $log): array => (new AuditLogResource($log))->resolve())
            ->all();

        return ApiResponse::paginated($paginator, AuditLogResource::class);
    }
}
