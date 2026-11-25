<?php

declare(strict_types=1);

use Illuminate\Support\Str;

it('returns the standard success shape', function () {
    $response = $this->getJson('/api/v1/health');

    $response->assertOk()
        ->assertJsonStructure(['data' => ['status', 'service', 'time']])
        ->assertJsonPath('data.status', 'ok')
        ->assertJsonPath('data.service', 'hms-backend');

    expect(array_keys($response->json()))->toBe(['data']);
});

it('returns the time as ISO 8601 UTC', function () {
    $time = $this->getJson('/api/v1/health')->json('data.time');

    expect($time)->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?Z$/');
});

it('includes a request id header', function () {
    $response = $this->getJson('/api/v1/health');

    expect(Str::isUuid($response->headers->get('X-Request-Id')))->toBeTrue();
});

it('is public and needs no login', function () {
    $this->getJson('/api/v1/health')->assertOk();
});

it('is named v1.health', function () {
    expect(route('v1.health', absolute: false))->toBe('/api/v1/health');
});
