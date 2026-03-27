<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Email tidak ditemukan dalam sistem.',
        ]);

        $email = $request->email;
        $user  = User::where('email', $email)->first();

        // Generate OTP 6 digit
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Hapus OTP lama, simpan yang baru (hashed)
        DB::table('password_reset_otps')->where('email', $email)->delete();
        DB::table('password_reset_otps')->insert([
            'email'      => $email,
            'otp'        => Hash::make($otp),
            'expires_at' => now()->addMinutes(15),
        ]);

        // Kirim email
        Mail::to($email)->send(new OtpMail($otp, $user?->name ?? ''));

        // Simpan email di session agar bisa dipakai di halaman verify-otp
        session(['otp_email' => $email]);

        return redirect()->route('otp.verify')
            ->with('status', 'Kode verifikasi telah dikirim ke email Anda.');
    }
}
