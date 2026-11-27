<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

/**
 * Stable machine-readable error codes (HMS documentation, Section 14).
 *
 * The frontend switches on these values, so never rename one.
 * NOT_FOUND and METHOD_NOT_ALLOWED are additions: Section 14 lists 404 as a
 * status code but gives it no error code.
 */
enum ErrorCode: string
{
    case Unauthenticated = 'UNAUTHENTICATED';
    case Forbidden = 'FORBIDDEN';
    case NotFound = 'NOT_FOUND';
    case MethodNotAllowed = 'METHOD_NOT_ALLOWED';
    case ValidationFailed = 'VALIDATION_FAILED';
    case SlotTaken = 'SLOT_TAKEN';
    case DoctorUnavailable = 'DOCTOR_UNAVAILABLE';
    case BookingLimitReached = 'BOOKING_LIMIT_REACHED';
    case TooLateToCancel = 'TOO_LATE_TO_CANCEL';
    case InvalidState = 'INVALID_STATE';
    case DuplicatePatient = 'DUPLICATE_PATIENT';
    case InvoiceLocked = 'INVOICE_LOCKED';
    case Overpayment = 'OVERPAYMENT';
    case BedOccupied = 'BED_OCCUPIED';
    case PatientAlreadyAdmitted = 'PATIENT_ALREADY_ADMITTED';
    case ApprovalRequired = 'APPROVAL_REQUIRED';
    case RateLimited = 'RATE_LIMITED';
    case ServerError = 'SERVER_ERROR';

    /** Default HTTP status for this code. */
    public function status(): int
    {
        return match ($this) {
            self::Unauthenticated => 401,
            self::Forbidden => 403,
            self::NotFound => 404,
            self::MethodNotAllowed => 405,
            self::SlotTaken,
            self::InvalidState,
            self::DuplicatePatient => 409,
            self::InvoiceLocked,
            self::BedOccupied,
            self::PatientAlreadyAdmitted,
            self::ApprovalRequired => 409,
            self::ValidationFailed,
            self::DoctorUnavailable,
            self::BookingLimitReached,
            self::TooLateToCancel,
            self::Overpayment => 422,
            self::RateLimited => 429,
            self::ServerError => 500,
        };
    }

    /** Safe, human-readable default message. Never include internal details here. */
    public function defaultMessage(): string
    {
        return match ($this) {
            self::Unauthenticated => 'You are not logged in or your session has expired.',
            self::Forbidden => 'You do not have permission to perform this action.',
            self::NotFound => 'The requested resource was not found.',
            self::MethodNotAllowed => 'This method is not allowed for the requested resource.',
            self::ValidationFailed => 'The submitted data is invalid.',
            self::SlotTaken => 'This slot is no longer available.',
            self::DoctorUnavailable => 'The doctor is not available at the requested time.',
            self::BookingLimitReached => 'The booking limit for this patient has been reached.',
            self::TooLateToCancel => 'It is too late to cancel or reschedule this appointment.',
            self::InvalidState => 'This action is not allowed in the current status.',
            self::DuplicatePatient => 'A patient with matching details already exists.',
            self::InvoiceLocked => 'This invoice can no longer be changed.',
            self::Overpayment => 'The payment would exceed the invoice total.',
            self::BedOccupied => 'This bed is not available.',
            self::PatientAlreadyAdmitted => 'This patient already has an active admission.',
            self::ApprovalRequired => 'This action needs approval from an Admin.',
            self::RateLimited => 'Too many requests. Please try again shortly.',
            self::ServerError => 'Something went wrong on our side. Please quote the request id when contacting support.',
        };
    }
}
