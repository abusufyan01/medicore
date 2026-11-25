<?php

declare(strict_types=1);

namespace App\Shared\Support\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gives every request a request id (HMS documentation, Section 21).
 *
 * - Reuses a client-supplied X-Request-Id only if it is a safe token,
 *   otherwise generates a UUID (prevents log injection).
 * - Adds the id to Laravel's Context so every log line written during the
 *   request carries it, including logs from queued jobs it dispatches.
 * - Returns the id in the X-Request-Id response header.
 */
final class AssignRequestId
{
    public const HEADER = 'X-Request-Id';

    public const CONTEXT_KEY = 'request_id';

    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $this->resolveId($request);

        $request->headers->set(self::HEADER, $requestId);
        $request->attributes->set('started_at', microtime(true));
        Context::add(self::CONTEXT_KEY, $requestId);

        $response = $next($request);
        $response->headers->set(self::HEADER, $requestId);

        return $response;
    }

    /**
     * One access-log line per request. Only the path is logged, never the
     * query string, because searches may contain patient names.
     */
    public function terminate(Request $request, Response $response): void
    {
        $startedAt = $request->attributes->get('started_at');

        Log::info('http.request', [
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'duration_ms' => is_float($startedAt)
                ? (int) round((microtime(true) - $startedAt) * 1000)
                : null,
        ]);
    }

    private function resolveId(Request $request): string
    {
        $incoming = $request->headers->get(self::HEADER);

        if (is_string($incoming) && preg_match('/^[A-Za-z0-9._-]{8,64}$/', $incoming) === 1) {
            return $incoming;
        }

        return (string) Str::uuid();
    }
}
