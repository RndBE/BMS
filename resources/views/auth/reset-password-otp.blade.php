<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi – {{ config('app.name') }}</title>
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
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/sandi-baru.svg') }}"
                alt="Buat Kata Sandi Baru"
                class="w-40 h-auto">
        </div>

        {{-- Heading --}}
        <h1 class="text-[28px] font-bold text-gray-900">Kata Sandi Baru</h1>
        <p class="text-[13.5px] text-gray-900 leading-relaxed mb-6">
            Silakan buat kata sandi baru untuk akun Anda.
        </p>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.store.otp') }}">
            @csrf

            {{-- Password baru --}}
            <div class="mb-2">
                <label for="password" class="block text-[13.5px] font-bold text-gray-900 mb-1.5">
                    Kata Sandi Baru
                </label>
                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Masukkan Kata Sandi Baru"
                        required
                        autocomplete="new-password"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-[14px] text-gray-900 placeholder-gray-400 pr-10
                               focus:outline-none focus:border-[#B40404] focus:ring-1 focus:ring-[#B40404]
                               transition-colors bg-white"
                    >
                    <button type="button" onclick="togglePwd('password', 'eye1')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 bg-transparent border-none cursor-pointer p-0">
                        <svg id="eye1" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi password --}}
            <div class="mb-5">
                <label for="password_confirmation" class="block text-[13.5px] font-bold text-gray-900 mb-1.5">
                    Konfirmasi Kata Sandi
                </label>
                <div class="relative">
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        placeholder="Konfirmasi Kata Sandi Baru"
                        required
                        autocomplete="new-password"
                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-[14px] text-gray-900 placeholder-gray-400 pr-10
                               focus:outline-none focus:border-[#B40404] focus:ring-1 focus:ring-[#B40404]
                               transition-colors bg-white"
                    >
                    <button type="button" onclick="togglePwd('password_confirmation', 'eye2')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 bg-transparent border-none cursor-pointer p-0">
                        <svg id="eye2" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full py-3 bg-[#B40404] hover:bg-[#9b0303] text-white text-[14.5px] font-semibold
                       rounded-lg border-none cursor-pointer transition-all duration-200 active:scale-[.99]">
                Simpan
            </button>
        </form>

    </div>

    <script>
    function togglePwd(fieldId, eyeId) {
        const input = document.getElementById(fieldId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
    </script>

</body>
</html>
