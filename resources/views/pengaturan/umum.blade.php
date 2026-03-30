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
                    <div class="relative custom-select-wrapper">
                        <select name="timezone" class="hidden real-select">
                            @foreach(['Asia/Jakarta' => 'Asia/Jakarta (GMT+7)', 'Asia/Makassar' => 'Asia/Makassar (GMT+8)', 'Asia/Jayapura' => 'Asia/Jayapura (GMT+9)'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('timezone', $timezone) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer">
                            <span class="select-text truncate text-left">Pilih...</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                </div>
            </div>

            {{-- Row 2: Format Tanggal / Format Jam --}}
            <div class="grid grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Format Tanggal</label>
                    <div class="relative custom-select-wrapper">
                        <select name="date_format" class="hidden real-select">
                            @foreach(['DD/MM/YYYY' => 'DD/MM/YYYY (01/02/2026)', 'MM/DD/YYYY' => 'MM/DD/YYYY (02/01/2026)', 'YYYY-MM-DD' => 'YYYY-MM-DD (2026-02-01)'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('date_format', $date_format) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer">
                            <span class="select-text truncate text-left">Pilih...</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Format Jam</label>
                    <div class="relative custom-select-wrapper">
                        <select name="time_format" class="hidden real-select">
                            <option value="24" {{ old('time_format', $time_format) === '24' ? 'selected' : '' }}>24 Jam (23:59)</option>
                            <option value="12" {{ old('time_format', $time_format) === '12' ? 'selected' : '' }}>12 Jam (11:59 PM)</option>
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer">
                            <span class="select-text truncate text-left">Pilih...</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                </div>
            </div>

            {{-- Row 3: Interval / Rentang Waktu --}}
            <div class="grid grid-cols-2 gap-5 mb-7">
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Interval Refresh Dashboard</label>
                    <div class="relative custom-select-wrapper">
                        <select name="refresh_interval" class="hidden real-select">
                            @foreach(['30' => '30 Detik', '60' => '1 Menit', '300' => '5 Menit', '86400' => '24 Jam (23:59)'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('refresh_interval', $refresh_interval) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer">
                            <span class="select-text truncate text-left">Pilih...</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Menentukan seberapa sering data pada dashboard diperbarui otomatis.</p>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Rentang Waktu Default Analisa</label>
                    <div class="relative custom-select-wrapper">
                        <select name="default_range" class="hidden real-select">
                            @foreach(['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('default_range', $default_range) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-[8px] text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer">
                            <span class="select-text truncate text-left">Pilih...</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-50 text-[13px] text-slate-700 dark:text-slate-200"></ul>
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

@push('scripts')
<script>
// ── Custom Select Initialization ─────────────────────────────────────────
(function() {
    document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
        const realSelect = wrapper.querySelector('.real-select');
        const btn = wrapper.querySelector('.select-btn');
        const text = wrapper.querySelector('.select-text');
        const dropdown = wrapper.querySelector('.select-dropdown');
        if (!realSelect || !btn || !text || !dropdown) return;

        const populateDropdown = () => {
            dropdown.innerHTML = '';
            const selectedOpt = realSelect.options[realSelect.selectedIndex];
            if (selectedOpt) text.textContent = selectedOpt.text;
            
            Array.from(realSelect.options).forEach((opt, index) => {
                const li = document.createElement('li');
                li.textContent = opt.text;
                li.className = 'px-3 py-2 cursor-pointer transition-colors ' + 
                    (index === realSelect.selectedIndex 
                        ? 'bg-red-50 text-red-700 font-medium dark:bg-red-900/30 dark:text-red-400' 
                        : 'hover:bg-red-50 hover:text-red-700 dark:hover:bg-[#3d3d3d] dark:hover:text-red-400');
                
                li.addEventListener('click', (e) => {
                    e.stopPropagation();
                    realSelect.selectedIndex = index;
                    text.textContent = opt.text;
                    dropdown.classList.add('hidden');
                    populateDropdown();
                    if (typeof realSelect.onchange === 'function') {
                        realSelect.onchange({ target: realSelect });
                    } else if (realSelect.getAttribute('onchange')) {
                        eval(realSelect.getAttribute('onchange'));
                    } else {
                        realSelect.dispatchEvent(new Event('change'));
                    }
                });
                dropdown.appendChild(li);
            });
        };
        
        populateDropdown();

        // Mencegat setter .value dari JS (berguna untuk modal edit)
        const originalSetter = Object.getOwnPropertyDescriptor(window.HTMLSelectElement.prototype, 'value') ? 
                               Object.getOwnPropertyDescriptor(window.HTMLSelectElement.prototype, 'value').set : null;
        if(originalSetter) {
            Object.defineProperty(realSelect, 'value', {
                set: function(val) {
                    originalSetter.call(this, val);
                    populateDropdown();
                },
                get: function() {
                    return Object.getOwnPropertyDescriptor(window.HTMLSelectElement.prototype, 'value').get.call(this);
                }
            });
        }
        
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isHidden = dropdown.classList.contains('hidden');
            document.querySelectorAll('.select-dropdown:not(.hidden)').forEach(d => {
                d.classList.add('hidden');
                d.parentElement.querySelector('.select-btn')?.classList.remove('border-red-400', 'ring-1', 'ring-red-400');
            });
            if (isHidden) {
                dropdown.classList.remove('hidden');
                btn.classList.add('border-red-400', 'ring-1', 'ring-red-400');
            } else {
                btn.classList.remove('border-red-400', 'ring-1', 'ring-red-400');
            }
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.select-dropdown').forEach(d => {
            d.classList.add('hidden');
            d.parentElement.querySelector('.select-btn')?.classList.remove('border-red-400', 'ring-1', 'ring-red-400');
        });
    });
})();
</script>
@endpush

@endsection
