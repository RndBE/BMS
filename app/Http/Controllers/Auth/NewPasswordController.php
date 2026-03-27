<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /** Tampilkan form reset password bawaan Laravel (token-based) */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /** Handle reset password bawaan Laravel (token-based) */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password'       => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }

    /** Tampilkan form buat password baru (OTP-based) */
    public function createOtp(Request $request): View|RedirectResponse
    {
        if (! session('otp_verified') || ! session('otp_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password-otp');
    }

    /** Handle simpan password baru (OTP-based) */
    public function storeOtp(Request $request): RedirectResponse
    {
        if (! session('otp_verified') || ! session('otp_email')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $email = session('otp_email');
        $user  = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Pengguna tidak ditemukan.']);
        }

        $user->forceFill([
            'password'       => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        // Bersihkan session & OTP dari DB
        DB::table('password_reset_otps')->where('email', $email)->delete();
        session()->forget(['otp_email', 'otp_verified']);

        return redirect()->route('login')
            ->with('status', 'Kata sandi berhasil diperbarui. Silakan login.');
    }
}
