<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#f0f0f0] flex items-center justify-center font-['Inter']">

    <div class="w-full max-w-md px-6">

        {{-- Ilustrasi --}}
        <div class="flex justify-center mb-8">
            <img src="{{ asset('images/lupa-password.svg') }}"
                alt="Lupa Kata Sandi"
                class="w-40 h-auto">
        </div>

        {{-- Heading --}}
        <h1 class="text-[28px] font-bold text-gray-900 mb-1">Lupa Kata Sandi</h1>
        <p class="text-[13.5px] text-gray-900 leading-relaxed mb-5">
            Masukkan alamat email anda untuk membuat kata sandi baru.
        </p>

        {{-- Status sukses --}}
        @if (session('status'))
            <div class="mb-5 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-[13px] text-left">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" class="text-left">
            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-[13.5px] font-semibold text-gray-700 mb-1.5">
                    Email
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Masukkan alamat email"
                    required
                    autofocus
                    autocomplete="email"
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-[14px] text-gray-900 placeholder-gray-400
                            focus:outline-none focus:border-[#B40404] focus:ring-1 focus:ring-[#B40404]
                            transition-colors bg-white"
                >
                @error('email')
                    <p class="mt-1.5 text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Kirim --}}
            <button
                type="submit"
                class="w-full py-3 bg-[#B40404] hover:bg-[#9b0303] active:scale-[.99]
                        text-white text-[14.5px] font-semibold rounded-lg
                        border-none cursor-pointer transition-all duration-200"
            >
                Kirim
            </button>
        </form>

        {{-- Link kembali --}}
        <a href="{{ route('login') }}"
           class="block mt-3 text-[13px] font-medium text-[#B40404] hover:underline no-underline text-center">
            Kembali ke Halaman Login
        </a>

    </div>

</body>
</html>
