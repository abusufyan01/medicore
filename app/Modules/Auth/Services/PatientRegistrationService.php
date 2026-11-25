<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Modules\Access\Models\Role;
use App\Modules\Auth\Models\User;
use App\Shared\Exceptions\DomainException;
use App\Shared\Exceptions\ErrorCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

class PatientRegistrationService
{
    /**
     * @param  array{name: string, email: string, phone: string, password: string}  $data
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
            ]);

            $patientRole = Role::where('name', 'patient')->firstOrFail();
            $user->roles()->attach($patientRole->id);

            event(new Registered($user));

            return $user->load('roles.permissions');
        });
    }

    public function verifyEmail(int $userId, string $hash): User
    {
        /** @var User|null $user */
        $user = User::find($userId);

        if ($user === null || ! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            throw new DomainException(
                ErrorCode::Forbidden,
                'Invalid or expired email verification link.'
            );
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return $user->load('roles.permissions');
    }

    public function resendVerification(string $email): void
    {
        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if ($user !== null && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
    }
}
