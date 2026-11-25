<?php

declare(strict_types=1);

namespace App\Shared\Support\Http;

use App\Shared\Exceptions\ErrorCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use stdClass;

/**
 * The only place that builds API response bodies (HMS documentation, Section 14).
 *
 *   Single record : { "data": {...} }
 *   List          : { "data": [...], "meta": { "page", "per_page", "total", "last_page" } }
 *   Error         : { "error": { "code", "message", "fields", "request_id" } }
 */
final class ApiResponse
{
    public static function success(mixed $data = null, int $status = 200): JsonResponse
    {
        return self::respond(['data' => $data], $status);
    }

    public static function created(mixed $data = null): JsonResponse
    {
        return self::success($data, 201);
    }

    public static function noContent(): Response
    {
        $response = response()->noContent();
        $response->headers->set(AssignRequestId::HEADER, self::requestId());

        return $response;
    }

    /**
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     * @param  class-string<JsonResource>|null  $resource  API Resource used to shape each item
     */
    public static function paginated(LengthAwarePaginator $paginator, ?string $resource = null): JsonResponse
    {
        $items = $paginator->items();

        $data = $resource === null
            ? $items
            : $resource::collection($items)->resolve(request());

        return self::respond([
            'data' => $data,
            'meta' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ], 200);
    }

    /**
     * @param  array<string, list<string>>  $fields  field name => messages (validation errors)
     */
    public static function error(
        ErrorCode $code,
        ?string $message = null,
        array $fields = [],
        ?int $status = null,
    ): JsonResponse {
        $requestId = self::requestId();

        return self::respond([
            'error' => [
                'code' => $code->value,
                'message' => $message ?? $code->defaultMessage(),
                // Always an object ({}), never an empty list ([]), so the contract stays stable.
                'fields' => $fields === [] ? new stdClass : $fields,
                'request_id' => $requestId,
            ],
        ], $status ?? $code->status(), $requestId);
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private static function respond(array $body, int $status, ?string $requestId = null): JsonResponse
    {
        return response()->json($body, $status, [
            AssignRequestId::HEADER => $requestId ?? self::requestId(),
        ]);
    }

    private static function requestId(): string
    {
        $id = Context::get(AssignRequestId::CONTEXT_KEY);

        // The middleware always sets it for HTTP requests; the fallback only
        // matters outside the request pipeline (for example in unit tests).
        return is_string($id) && $id !== '' ? $id : (string) Str::uuid();
    }
}
