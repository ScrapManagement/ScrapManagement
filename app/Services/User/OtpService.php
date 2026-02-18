<?php

namespace App\Services\User;

use App\Models\User\User;
use App\Models\User\OtpCode;
use Illuminate\Support\Facades\Hash;


class OtpService
{
    public function generate(User $user): string
    {
        $code = rand(100000, 999999);

        OtpCode::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->delete();

        OtpCode::create([
            'user_id' => $user->id,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(2),
        ]);

        return $code;
    }

    public function verify(User $user, string $code): bool
    {
        $otp = OtpCode::where('user_id', $user->id)
            ->where('code', $code)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp) {
            return false;
        }

        $otp->update(['verified_at' => now()]);
        $user->update(['phone_verified_at' => now()]);

        return true;
    }
}
