<?php

declare(strict_types=1);

use App\Shared\Exceptions\ErrorCode;
use App\Shared\Support\Http\ApiResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Context;

beforeEach(function () {
    Context::add('request_id', 'test-request-id-1');
});

it('wraps a single record in data', function () {
    $response = ApiResponse::success(['id' => 1, 'name' => 'Ayesha']);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData(true))->toBe(['data' => ['id' => 1, 'name' => 'Ayesha']])
        ->and($response->headers->get('X-Request-Id'))->toBe('test-request-id-1');
});

it('returns 201 for created records', function () {
    expect(ApiResponse::created(['id' => 7])->getStatusCode())->toBe(201);
});

it('returns 204 with no body', function () {
    $response = ApiResponse::noContent();

    expect($response->getStatusCode())->toBe(204)
        ->and($response->getContent())->toBe('')
        ->and($response->headers->get('X-Request-Id'))->toBe('test-request-id-1');
});

it('builds list meta with page, per_page, total and last_page', function () {
    $paginator = new LengthAwarePaginator([['id' => 26], ['id' => 27]], 53, 25, 2);

    $body = ApiResponse::paginated($paginator)->getData(true);

    expect($body['data'])->toBe([['id' => 26], ['id' => 27]])
        ->and($body['meta'])->toBe([
            'page' => 2,
            'per_page' => 25,
            'total' => 53,
            'last_page' => 3,
        ]);
});

it('builds the standard error shape from an error code', function () {
    $response = ApiResponse::error(ErrorCode::SlotTaken);
    $error = $response->getData(true)['error'];

    expect($response->getStatusCode())->toBe(409)
        ->and($error['code'])->toBe('SLOT_TAKEN')
        ->and($error['message'])->toBe(ErrorCode::SlotTaken->defaultMessage())
        ->and($error['request_id'])->toBe('test-request-id-1')
        ->and($response->headers->get('X-Request-Id'))->toBe('test-request-id-1');
});

it('always encodes empty fields as an object, never a list', function () {
    $json = ApiResponse::error(ErrorCode::Forbidden)->getContent();

    expect($json)->toContain('"fields":{}');
});

it('lists field messages for validation errors', function () {
    $response = ApiResponse::error(
        ErrorCode::ValidationFailed,
        fields: ['email' => ['The email field is required.']],
    );

    expect($response->getStatusCode())->toBe(422)
        ->and($response->getData(true)['error']['fields'])
        ->toBe(['email' => ['The email field is required.']]);
});

it('allows a custom message and status', function () {
    $response = ApiResponse::error(ErrorCode::InvalidState, 'Only scheduled appointments can be checked in.', status: 409);

    expect($response->getData(true)['error']['message'])->toBe('Only scheduled appointments can be checked in.');
});
