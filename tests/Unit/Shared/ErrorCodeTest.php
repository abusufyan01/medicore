<?php

declare(strict_types=1);

use App\Shared\Exceptions\ErrorCode;

it('contains every error code from Section 14', function () {
    $documented = [
        'UNAUTHENTICATED', 'FORBIDDEN', 'VALIDATION_FAILED', 'SLOT_TAKEN',
        'DOCTOR_UNAVAILABLE', 'BOOKING_LIMIT_REACHED', 'TOO_LATE_TO_CANCEL',
        'INVALID_STATE', 'DUPLICATE_PATIENT', 'INVOICE_LOCKED', 'OVERPAYMENT',
        'BED_OCCUPIED', 'PATIENT_ALREADY_ADMITTED', 'APPROVAL_REQUIRED',
        'RATE_LIMITED', 'SERVER_ERROR',
    ];

    $actual = array_map(fn (ErrorCode $code) => $code->value, ErrorCode::cases());

    expect($actual)->toContain(...$documented);
});

it('maps each documented code to its documented HTTP status', function (ErrorCode $code, int $status) {
    expect($code->status())->toBe($status);
})->with([
    [ErrorCode::Unauthenticated, 401],
    [ErrorCode::Forbidden, 403],
    [ErrorCode::ValidationFailed, 422],
    [ErrorCode::SlotTaken, 409],
    [ErrorCode::DoctorUnavailable, 422],
    [ErrorCode::BookingLimitReached, 422],
    [ErrorCode::TooLateToCancel, 422],
    [ErrorCode::InvalidState, 409],
    [ErrorCode::DuplicatePatient, 409],
    [ErrorCode::InvoiceLocked, 409],
    [ErrorCode::Overpayment, 422],
    [ErrorCode::BedOccupied, 409],
    [ErrorCode::PatientAlreadyAdmitted, 409],
    [ErrorCode::ApprovalRequired, 409],
    [ErrorCode::RateLimited, 429],
    [ErrorCode::ServerError, 500],
]);

it('has a non-empty default message and an error status for every code', function () {
    foreach (ErrorCode::cases() as $code) {
        expect($code->defaultMessage())->not->toBeEmpty()
            ->and($code->status())->toBeGreaterThanOrEqual(400);
    }
});
