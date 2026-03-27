<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>

    <div class="w-full max-w-md px-6">

        {{-- Ikon --}}
        <div class="flex justify-center mb-8">
            <img src="{{ asset('images/lupa-password.svg') }}"
                alt="Verifikasi OTP"
                class="w-40 h-auto">
        </div>

        {{-- Heading --}}
        <h1 class="text-[28px] font-bold text-gray-900 mb-1">Masukkan Kode</h1>
        <p class="text-[13.5px] text-gray-900 leading-relaxed mb-5">
            Kami telah mengirimkan kode verifikasi ke
            <span class="font-bold text-gray-900">{{ session('otp_email') }}</span>.
        </p>

        {{-- Status --}}
        @if (session('status'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-[13px]">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form OTP --}}
        <form method="POST" action="{{ route('otp.check') }}">
            @csrf

            <div class="mb-5">
                <!-- <label for="otp" class="block text-[13.5px] font-semibold text-gray-700 mb-2">
                    Kode Verifikasi
                </label> -->

                {{-- 6 kotak OTP --}}
                <div class="flex gap-2 justify-between" id="otp-boxes">
                    @for ($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            maxlength="1"
                            inputmode="numeric"
                            pattern="[0-9]"
                            class="otp-input w-full aspect-square text-center text-[24px] font-bold border border-gray-200 rounded-xl
                                   focus:outline-none focus:border-[#B40404] focus:ring-2 focus:ring-[#B40404]/20
                                   bg-white text-gray-900 transition-all"
                            autocomplete="off"
                        >
                    @endfor
                </div>
                {{-- Hidden input gabungan --}}
                <input type="hidden" name="otp" id="otp-combined">

                @error('otp')
                    <p class="mt-2 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-3 bg-[#B40404] hover:bg-[#9b0303] text-white text-[14.5px] font-semibold
                       rounded-lg border-none cursor-pointer transition-all duration-200 active:scale-[.99]">
                Verifikasi
            </button>
        </form>

        {{-- Kirim ulang --}}
        <div class="mt-3 text-center text-[13px] text-gray-500">
            Tidak menerima kode?
            <form method="POST" action="{{ route('otp.resend') }}" class="inline">
                @csrf
                <button type="submit" id="resend-btn"
                    class="text-[#B40404] font-semibold hover:underline bg-transparent border-none cursor-pointer text-[13px]">
                    Kirim ulang kode
                </button>
            </form>
        </div>
    </div>

    <script>
    // Auto-jump antar kotak OTP + gabung ke hidden input saat submit
    const boxes = document.querySelectorAll('.otp-input');
    const combined = document.getElementById('otp-combined');

    boxes.forEach((box, idx) => {
        box.addEventListener('input', e => {
            const val = e.target.value.replace(/\D/g, '');
            e.target.value = val.slice(-1);
            if (val && idx < boxes.length - 1) boxes[idx + 1].focus();
            updateCombined();
        });
        box.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                boxes[idx - 1].focus();
            }
            if (e.key === 'ArrowLeft'  && idx > 0)                boxes[idx - 1].focus();
            if (e.key === 'ArrowRight' && idx < boxes.length - 1) boxes[idx + 1].focus();
        });
        box.addEventListener('paste', e => {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            boxes.forEach((b, i) => { b.value = pasted[i] || ''; });
            updateCombined();
            const lastFilled = Math.min(pasted.length, boxes.length) - 1;
            boxes[lastFilled]?.focus();
        });
    });

    function updateCombined() {
        combined.value = Array.from(boxes).map(b => b.value).join('');
    }

    // Gabung sebelum submit
    document.querySelector('form').addEventListener('submit', () => updateCombined());
    </script>

</body>
</html>
