<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

it('generates a request id when the client sends none', function () {
    $id = $this->getJson('/api/v1/health')->headers->get('X-Request-Id');

    expect(Str::isUuid($id))->toBeTrue();
});

it('reuses a safe request id sent by the client', function () {
    $response = $this->getJson('/api/v1/health', ['X-Request-Id' => 'frontend-trace-12345']);

    expect($response->headers->get('X-Request-Id'))->toBe('frontend-trace-12345');
});

it('replaces an unsafe request id with a generated one', function (string $unsafe) {
    $id = $this->getJson('/api/v1/health', ['X-Request-Id' => $unsafe])->headers->get('X-Request-Id');

    expect($id)->not->toBe($unsafe)
        ->and(Str::isUuid($id))->toBeTrue();
})->with([
    'too short' => 'short',
    'contains spaces' => 'has spaces in it',
    'contains symbols' => 'bad<script>id',
    'too long' => str_repeat('a', 65),
]);

it('gives each request its own id', function () {
    $first = $this->getJson('/api/v1/health')->headers->get('X-Request-Id');
    $second = $this->getJson('/api/v1/health')->headers->get('X-Request-Id');

    expect($first)->not->toBe($second);
});

it('makes the request id available in the log context during the request', function () {
    Route::get('/_test/context', fn () => response()->json(['id' => Context::get('request_id')]));

    $response = $this->getJson('/_test/context');

    expect($response->json('id'))->toBe($response->headers->get('X-Request-Id'));
});

it('writes one access log line without the query string', function () {
    Log::spy();

    $this->getJson('/api/v1/health?q=Ayesha');

    Log::shouldHaveReceived('info')
        ->withArgs(fn (string $message, array $context = []) => $message === 'http.request'
            && $context['method'] === 'GET'
            && $context['path'] === 'api/v1/health'
            && $context['status'] === 200
            && ! array_key_exists('query', $context))
        ->once();
});
