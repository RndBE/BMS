@extends('layouts.app')

@section('page-title', 'Umum')

@section('content')

@if(session('success'))
<div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-[13px] font-medium px-4 py-3 rounded-lg">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 12 4 10"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="max-w-2xl">
    <div class="bg-white dark:bg-[#232323] dark:border dark:border-[#2d2d2d] rounded-xl border border-slate-100 shadow-sm px-8 py-7">

        {{-- Header --}}
        <div class="mb-6">
            <div class="text-[18px] font-bold text-slate-800 dark:text-white">Informasi Dasar & Lokalisasi</div>
            <div class="text-[13px] text-slate-400 mt-0.5">Konfigurasikan informasi dasar dan preferensi tampilan sistem Anda</div>
        </div>

        <form method="POST" action="{{ route('pengaturan.umum.save') }}">
            @csrf

            {{-- Row 1: Nama Site / Zona Waktu --}}
            <div class="grid grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nama Site/Kantor</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $site_name) }}"
                        class="w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors @error('site_name') border-red-400 @enderror">
                    @error('site_name')<p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Zona Waktu</label>
                    <div class="relative">
                        <select name="timezone"
                            class="w-full appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 transition-colors pr-8">
                            @foreach(['Asia/Jakarta' => 'Asia/Jakarta (GMT+7)', 'Asia/Makassar' => 'Asia/Makassar (GMT+8)', 'Asia/Jayapura' => 'Asia/Jayapura (GMT+9)'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('timezone', $timezone) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            {{-- Row 2: Format Tanggal / Format Jam --}}
            <div class="grid grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Format Tanggal</label>
                    <div class="relative">
                        <select name="date_format"
                            class="w-full appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 transition-colors pr-8">
                            @foreach(['DD/MM/YYYY' => 'DD/MM/YYYY (01/02/2026)', 'MM/DD/YYYY' => 'MM/DD/YYYY (02/01/2026)', 'YYYY-MM-DD' => 'YYYY-MM-DD (2026-02-01)'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('date_format', $date_format) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Format Jam</label>
                    <div class="relative">
                        <select name="time_format"
                            class="w-full appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 transition-colors pr-8">
                            <option value="24" {{ old('time_format', $time_format) === '24' ? 'selected' : '' }}>24 Jam (23:59)</option>
                            <option value="12" {{ old('time_format', $time_format) === '12' ? 'selected' : '' }}>12 Jam (11:59 PM)</option>
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            {{-- Row 3: Interval / Rentang Waktu --}}
            <div class="grid grid-cols-2 gap-5 mb-7">
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Interval Refresh Dashboard</label>
                    <div class="relative">
                        <select name="refresh_interval"
                            class="w-full appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 transition-colors pr-8">
                            @foreach(['30' => '30 Detik', '60' => '1 Menit', '300' => '5 Menit', '86400' => '24 Jam (23:59)'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('refresh_interval', $refresh_interval) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Menentukan seberapa sering data pada dashboard diperbarui otomatis.</p>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Rentang Waktu Default Analisa</label>
                    <div class="relative">
                        <select name="default_range"
                            class="w-full appearance-none border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 transition-colors pr-8">
                            @foreach(['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('default_range', $default_range) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Rentang waktu yang diterapkan ketika membuka menu Analisa Data.</p>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex justify-end gap-3">
                <button type="reset"
                    class="px-10 py-[9px] rounded-lg border border-slate-200 dark:border-[#FFFFFF] dark:text-[#FFFFFF] dark:hover:bg-[#2a2a2a] text-[13px] font-semibold text-slate-700 bg-white dark:bg-transparent hover:bg-slate-50 transition-colors cursor-pointer">
                    Reset
                </button>
                <button type="submit"
                    class="px-10 py-[9px] rounded-lg bg-red-700 hover:bg-red-800 text-white text-[13px] font-semibold transition-colors cursor-pointer">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
