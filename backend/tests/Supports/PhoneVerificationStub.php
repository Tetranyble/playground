<?php

namespace Tests\Supports;

use App\Interfaces\PhoneVerificationInterface;
use App\Models\User;

class PhoneVerificationStub implements PhoneVerificationInterface
{
    use ResponseFormater;

    public function create(User $user, string $number): object|array
    {
        return tap($user)->update(['phone' => $number]);

    }

    public function verify(User $user, string $code): bool
    {
        $user->markPhoneAsVerified();

        return true;
    }
}
