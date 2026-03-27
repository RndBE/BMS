<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    /** Tampilkan halaman masukkan kode OTP */
    public function create(Request $request): View|RedirectResponse
    {
        if (! session('otp_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.verify-otp');
    }

    /** Validasi kode OTP yang dimasukkan user */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'min:6', 'max:6'],
        ], [
            'otp.required' => 'Kode verifikasi wajib diisi.',
            'otp.min'      => 'Kode verifikasi harus 6 digit.',
        ]);

        $email = session('otp_email');
        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['otp' => 'Sesi sudah berakhir, coba lagi.']);
        }

        $record = DB::table('password_reset_otps')->where('email', $email)->first();

        if (! $record) {
            return back()->withErrors(['otp' => 'Kode tidak valid atau sudah digunakan.']);
        }

        if (now()->gt($record->expires_at)) {
            DB::table('password_reset_otps')->where('email', $email)->delete();
            return back()->withErrors(['otp' => 'Kode sudah kadaluarsa. Silakan minta kode baru.']);
        }

        if (! Hash::check($request->otp, $record->otp)) {
            return back()->withErrors(['otp' => 'Kode verifikasi salah.']);
        }

        // OTP valid — simpan flag di session, arahkan ke reset password
        session(['otp_verified' => true]);

        return redirect()->route('password.reset.otp');
    }

    /** Kirim ulang OTP */
    public function resend(Request $request): RedirectResponse
    {
        $email = session('otp_email');
        if (! $email) {
            return redirect()->route('password.request');
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_otps')->where('email', $email)->delete();
        DB::table('password_reset_otps')->insert([
            'email'      => $email,
            'otp'        => Hash::make($otp),
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($email)->send(new OtpMail($otp));

        return back()->with('status', 'Kode baru telah dikirim ke email Anda.');
    }
}
