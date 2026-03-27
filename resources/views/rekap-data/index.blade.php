@extends('layouts.app')

@section('content')
<div x-data="rekapData()" class="space-y-5">

    {{-- Header --}}
    <div>
        <h1 class="text-[20px] font-bold text-slate-800 dark:text-slate-100">Rekap Data</h1>
        <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-0.5">Rekap kelengkapan data sensor per ruangan dalam rentang tanggal</p>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-[#2a2a2a] rounded-xl border border-slate-200 dark:border-[#1D1D1D] shadow-sm px-6 py-5">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">

            {{-- Range Picker --}}
            <div class="flex-1 min-w-0">
                <label class="block text-[12.5px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Rentang Tanggal</label>
                <div class="relative w-full" id="rkpWrap">
                    <input type="text" id="rkpRangeText" readonly autocomplete="off"
                        placeholder="YYYY-MM-DD — YYYY-MM-DD"
                        class="w-full h-10 rounded-lg border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#1D1D1D] dark:text-slate-300 px-3.5 pr-10 text-[13px] text-slate-700 focus:outline-none focus:border-[#B40404] hover:border-slate-300 transition-colors bg-white cursor-pointer">
                    <button type="button" id="rkpBtn"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-[#B40404]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </button>

                    {{-- Floating Panel --}}
                    <div id="rkpPanel"
                        class="fixed w-[calc(100vw-32px)] max-w-[640px] rounded-xl border border-slate-200 bg-white dark:bg-[#2a2a2a] dark:border-[#3d3d3d] shadow-xl p-4 z-[9999] hidden">

                        {{-- Start/End boxes --}}
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <div id="rkpStartBox" class="h-9 rounded-lg border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#1D1D1D] flex items-center justify-center text-[13px] text-slate-700 dark:text-slate-300"></div>
                            </div>
                            <div class="w-8 flex items-center justify-center text-slate-400">→</div>
                            <div class="flex-1">
                                <div id="rkpEndBox" class="h-9 rounded-lg border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#1D1D1D] flex items-center justify-center text-[13px] text-slate-700 dark:text-slate-300"></div>
                            </div>
                        </div>
                        <div class="mt-1.5 text-center text-[12px] text-slate-500">
                            <span id="rkpDays">0 hari</span>
                        </div>

                        {{-- Dual calendar --}}
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Left --}}
                            <div class="rounded-xl border border-slate-200 dark:border-[#3d3d3d] p-3">
                                <div class="flex items-center justify-between">
                                    <button type="button" id="rkpPrev" class="h-8 w-8 rounded-lg border border-slate-200 dark:border-[#3d3d3d] hover:bg-slate-50 dark:hover:bg-[#1D1D1D] flex items-center justify-center text-slate-600 dark:text-slate-300">‹</button>
                                    <div class="flex items-center gap-2">
                                        <div class="relative">
                                            <button type="button" id="rkpMonthBtnL" class="h-7 rounded-full bg-slate-100 dark:bg-[#1D1D1D] px-3 text-[12px] font-semibold text-slate-700 dark:text-slate-300 inline-flex items-center gap-1 hover:bg-slate-200">
                                                <span id="rkpMonthLabelL"></span>
                                                <svg width="10" height="10" viewBox="0 0 20 20" fill="none"><path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                            </button>
                                            <div id="rkpMonthMenuL" class="absolute left-0 mt-1 w-36 rounded-xl border border-slate-200 dark:border-[#3d3d3d] bg-white dark:bg-[#2a2a2a] shadow-lg p-1 hidden z-50 max-h-48 overflow-auto">
                                                <div id="rkpMonthItemsL" class="text-[12px]"></div>
                                            </div>
                                        </div>
                                        <div class="relative">
                                            <button type="button" id="rkpYearBtnL" class="h-7 rounded-full bg-slate-100 dark:bg-[#1D1D1D] px-3 text-[12px] font-semibold text-slate-700 dark:text-slate-300 inline-flex items-center gap-1 hover:bg-slate-200">
                                                <span id="rkpYearLabelL"></span>
                                                <svg width="10" height="10" viewBox="0 0 20 20" fill="none"><path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                            </button>
                                            <div id="rkpYearMenuL" class="absolute left-0 mt-1 w-24 rounded-xl border border-slate-200 dark:border-[#3d3d3d] bg-white dark:bg-[#2a2a2a] shadow-lg p-1 hidden z-50 max-h-48 overflow-auto">
                                                <div id="rkpYearItemsL" class="text-[12px]"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-8"></div>
                                </div>
                                <div class="mt-3 grid grid-cols-7 text-[11px] text-slate-400 text-center">
                                    <div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div><div>Min</div>
                                </div>
                                <div id="rkpGridL" class="mt-1 grid grid-cols-7"></div>
                            </div>

                            {{-- Right --}}
                            <div class="rounded-xl border border-slate-200 dark:border-[#3d3d3d] p-3">
                                <div class="flex items-center justify-between">
                                    <div class="w-8"></div>
                                    <div class="flex items-center gap-2">
                                        <div class="relative">
                                            <button type="button" id="rkpMonthBtnR" class="h-7 rounded-full bg-slate-100 dark:bg-[#1D1D1D] px-3 text-[12px] font-semibold text-slate-700 dark:text-slate-300 inline-flex items-center gap-1 hover:bg-slate-200">
                                                <span id="rkpMonthLabelR"></span>
                                                <svg width="10" height="10" viewBox="0 0 20 20" fill="none"><path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                            </button>
                                            <div id="rkpMonthMenuR" class="absolute left-0 mt-1 w-36 rounded-xl border border-slate-200 dark:border-[#3d3d3d] bg-white dark:bg-[#2a2a2a] shadow-lg p-1 hidden z-50 max-h-48 overflow-auto">
                                                <div id="rkpMonthItemsR" class="text-[12px]"></div>
                                            </div>
                                        </div>
                                        <div class="relative">
                                            <button type="button" id="rkpYearBtnR" class="h-7 rounded-full bg-slate-100 dark:bg-[#1D1D1D] px-3 text-[12px] font-semibold text-slate-700 dark:text-slate-300 inline-flex items-center gap-1 hover:bg-slate-200">
                                                <span id="rkpYearLabelR"></span>
                                                <svg width="10" height="10" viewBox="0 0 20 20" fill="none"><path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                            </button>
                                            <div id="rkpYearMenuR" class="absolute left-0 mt-1 w-24 rounded-xl border border-slate-200 dark:border-[#3d3d3d] bg-white dark:bg-[#2a2a2a] shadow-lg p-1 hidden z-50 max-h-48 overflow-auto">
                                                <div id="rkpYearItemsR" class="text-[12px]"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="rkpNext" class="h-8 w-8 rounded-lg border border-slate-200 dark:border-[#3d3d3d] hover:bg-slate-50 dark:hover:bg-[#1D1D1D] flex items-center justify-center text-slate-600 dark:text-slate-300">›</button>
                                </div>
                                <div class="mt-3 grid grid-cols-7 text-[11px] text-slate-400 text-center">
                                    <div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div><div>Min</div>
                                </div>
                                <div id="rkpGridR" class="mt-1 grid grid-cols-7"></div>
                            </div>
                        </div>

                        {{-- Panel Footer --}}
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" id="rkpClear" class="px-4 py-2 text-[12px] rounded-lg border border-slate-200 dark:border-[#3d3d3d] text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-[#1D1D1D]">Batal</button>
                            <button type="button" id="rkpApply" class="px-4 py-2 text-[12px] rounded-lg bg-[#B40404] text-white hover:bg-[#9b0303]">Terapkan</button>
                        </div>
                    </div>

                    <input type="hidden" id="rkpStartHidden" x-ref="startDate">
                    <input type="hidden" id="rkpEndHidden"   x-ref="endDate">
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <button @click="fetchData()"
                    class="h-10 px-6 rounded-lg bg-[#B40404] hover:bg-[#9b0303] text-white text-[13px] font-semibold flex items-center gap-2 transition-all active:scale-[.99]">
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <svg x-show="loading" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                    <span x-show="!loading">Cari</span>
                    <span x-show="loading" x-cloak>Memuat...</span>
                </button>
                <button @click="resetFilter()"
                    class="h-10 px-5 rounded-lg border border-slate-200 dark:border-[#3d3d3d] dark:text-slate-300 text-slate-600 text-[13px] font-medium hover:bg-slate-50 dark:hover:bg-[#1D1D1D] transition-colors">
                    Reset
                </button>
            </div>
        </div>

        <div x-show="errorMessage" x-cloak class="mt-4 px-4 py-3 bg-red-50 border-l-4 border-red-500 rounded-lg text-[13px] text-red-700" x-text="errorMessage"></div>
    </div>

    {{-- Summary Cards --}}
    <div x-show="dataLoaded" x-cloak class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#2a2a2a] rounded-xl border border-slate-200 dark:border-[#1D1D1D] px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold text-[#B40404] uppercase tracking-wide">Total Ruangan</p>
            <p class="text-[22px] font-bold text-slate-800 dark:text-slate-100 mt-1.5" x-text="rooms.length"></p>
        </div>
        <div class="bg-white dark:bg-[#2a2a2a] rounded-xl border border-slate-200 dark:border-[#1D1D1D] px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wide">Rata-rata Kelengkapan</p>
            <p class="text-[22px] font-bold text-slate-800 dark:text-slate-100 mt-1.5" x-text="overallAvg + '%'"></p>
        </div>
        <div class="bg-white dark:bg-[#2a2a2a] rounded-xl border border-slate-200 dark:border-[#1D1D1D] px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold text-purple-600 uppercase tracking-wide">Rentang Hari</p>
            <p class="text-[22px] font-bold text-slate-800 dark:text-slate-100 mt-1.5" x-text="dates.length + ' hari'"></p>
        </div>
        <div class="bg-white dark:bg-[#2a2a2a] rounded-xl border border-slate-200 dark:border-[#1D1D1D] px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold text-orange-500 uppercase tracking-wide">Ruangan Kurang Lengkap</p>
            <p class="text-[22px] font-bold text-slate-800 dark:text-slate-100 mt-1.5" x-text="rooms.filter(r => r.overall_pct < 95).length"></p>
        </div>
    </div>

    {{-- Main Table --}}
    <div x-show="dataLoaded" x-cloak class="bg-white dark:bg-[#2a2a2a] rounded-xl border border-slate-200 dark:border-[#1D1D1D] shadow-sm overflow-hidden">
        {{-- Legend --}}
        <div class="px-6 py-3 bg-slate-50 dark:bg-[#1D1D1D] border-b border-slate-200 dark:border-[#222] flex flex-wrap items-center gap-4">
            <span class="text-[11.5px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wide mr-1">Keterangan:</span>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span><span class="text-[12px] text-slate-600 dark:text-slate-400">≥ 95% (Baik)</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span><span class="text-[12px] text-slate-600 dark:text-slate-400">60–94% (Sedang)</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span><span class="text-[12px] text-slate-600 dark:text-slate-400">< 60% (Kurang)</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span><span class="text-[12px] text-slate-600 dark:text-slate-400">Belum ada data</span></div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-[13px] whitespace-nowrap border-separate border-spacing-0">
                <thead class="text-[11px] font-bold text-slate-800 dark:text-slate-200 uppercase">
                    {{-- Row 1: Bulan --}}
                    <tr class="bg-red-50 dark:bg-[#1D1D1D]">
                        <th class="sticky left-0 z-30 bg-red-50 dark:bg-[#1D1D1D] px-4 py-3 text-left min-w-[10rem]" rowspan="2">Ruangan</th>
                        <template x-for="group in monthGroups" :key="group.label">
                            <th class="px-4 py-3 text-center border-l-2 border-red-100 dark:border-[#222]" :colspan="group.count" x-text="group.label"></th>
                        </template>
                    </tr>
                    {{-- Row 2: Tanggal --}}
                    <tr class="bg-red-50/60 dark:bg-[#232323]">
                        <template x-for="date in dates" :key="date">
                            <th class="px-3 py-2 text-center min-w-[5.5rem] border-l border-red-100 dark:border-[#222]" x-text="formatDay(date)"></th>
                        </template>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-[#1D1D1D] bg-white dark:bg-[#2a2a2a]">
                    <template x-for="(room, idx) in rooms" :key="room.id">
                        <tr class="hover:bg-slate-50 dark:hover:bg-[#1D1D1D] transition-colors">
                            <td class="sticky left-0 z-10 bg-white dark:bg-[#2a2a2a] px-4 py-3 font-semibold text-slate-800 dark:text-slate-200 border-r border-slate-100 dark:border-[#222]" style="box-shadow: 4px 0 6px rgba(0,0,0,0.06)" x-text="room.name"></td>
                            <template x-for="day in room.days" :key="day.date">
                                <td class="px-4 py-3 text-center">
                                    <template x-if="day.expected === 0">
                                        <span class="text-slate-400 text-[12px]">—</span>
                                    </template>
                                    <template x-if="day.expected > 0">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-[13px] font-bold" :class="pctTextClass(day.pct)" x-text="day.pct + '%'"></span>
                                            <div class="w-full h-1.5 rounded-full bg-slate-200 dark:bg-[#3d3d3d] overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-500" :class="pctBarClass(day.pct)" :style="`width: ${Math.min(100, day.pct)}%`"></div>
                                            </div>
                                            <span class="text-[10px] text-slate-400">
                                                <span x-text="day.count.toLocaleString('id-ID')"></span>/<span x-text="day.expected.toLocaleString('id-ID')"></span>
                                            </span>
                                        </div>
                                    </template>
                                </td>
                            </template>
                        </tr>
                    </template>
                    <template x-if="rooms.length === 0 && dataLoaded">
                        <tr>
                            <td :colspan="2 + dates.length" class="px-5 py-12 text-center text-slate-400 text-[13px]">
                                Tidak ada data untuk rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Empty State --}}
    <div x-show="!dataLoaded && !loading" x-cloak
        class="bg-white dark:bg-[#2a2a2a] rounded-xl border border-slate-200 dark:border-[#1D1D1D] shadow-sm py-16 px-6 text-center">
        <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-[#B40404]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="text-[15px] font-bold text-slate-800 dark:text-slate-200 mb-1">Rekap Data Kelengkapan</h3>
        <p class="text-[13px] text-slate-500 dark:text-slate-400 max-w-xs mx-auto">Pilih rentang tanggal lalu klik <strong>Cari</strong> untuk menampilkan rekap kelengkapan data seluruh ruangan.</p>
    </div>

</div>

@push('scripts')
<script>
function rekapData() {
    const today = new Date();
    const ago6  = new Date(today); ago6.setDate(today.getDate() - 6);
    const fmt   = d => d.toISOString().split('T')[0];

    return {
        filters:      { start_date: fmt(ago6), end_date: fmt(today) },
        loading:      false,
        dataLoaded:   false,
        errorMessage: '',
        dates:        [],
        rooms:        [],

        get overallAvg() {
            if (!this.rooms.length) return 0;
            const sum = this.rooms.reduce((a, r) => a + r.overall_pct, 0);
            return Math.round(sum / this.rooms.length * 10) / 10;
        },

        async fetchData() {
            const sd = document.getElementById('rkpStartHidden').value || this.filters.start_date;
            const ed = document.getElementById('rkpEndHidden').value   || this.filters.end_date;

            if (!sd || !ed) { this.errorMessage = 'Pilih rentang tanggal terlebih dahulu.'; return; }

            const diff = Math.round((new Date(ed) - new Date(sd)) / 86400000);
            if (diff < 0) { this.errorMessage = 'Tanggal mulai harus sebelum tanggal akhir.'; return; }
            if (diff > 30) { this.errorMessage = 'Rentang tanggal maksimal 31 hari.'; return; }

            this.filters.start_date = sd;
            this.filters.end_date   = ed;
            this.loading      = true;
            this.errorMessage = '';
            this.dataLoaded   = false;
            this.dates        = [];
            this.rooms        = [];

            try {
                const res  = await fetch(`/api/rekap-data?start_date=${sd}&end_date=${ed}`, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                if (res.ok && json.success) {
                    this.dates      = json.dates;
                    this.rooms      = json.rooms;
                    this.dataLoaded = true;
                } else {
                    this.errorMessage = json.message || 'Gagal memuat data.';
                }
            } catch (e) {
                this.errorMessage = 'Terjadi kesalahan saat menghubungi server.';
            } finally {
                this.loading = false;
            }
        },

        resetFilter() {
            const today = new Date(), ago6 = new Date(today);
            ago6.setDate(today.getDate() - 6);
            const fmt = d => d.toISOString().split('T')[0];
            const sd = fmt(ago6), ed = fmt(today);
            this.filters    = { start_date: sd, end_date: ed };
            this.dataLoaded = false;
            this.dates      = []; this.rooms = [];
            this.errorMessage = '';
            if (typeof window.rkpSetRange === 'function') window.rkpSetRange(sd, ed);
        },

        formatDay(dateStr) {
            return new Date(dateStr + 'T00:00:00').toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
        },

        get monthGroups() {
            if (!this.dates.length) return [];
            const groups = [];
            let cur = null;
            for (const d of this.dates) {
                const label = new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
                if (cur && cur.label === label) { cur.count++; }
                else { cur = { label, count: 1 }; groups.push(cur); }
            }
            return groups;
        },

        pctBarClass(pct)  { return pct >= 95 ? 'bg-emerald-500' : pct >= 60 ? 'bg-yellow-400' : 'bg-red-500'; },
        pctTextClass(pct) { return pct >= 95 ? 'text-emerald-600' : pct >= 60 ? 'text-yellow-600' : 'text-red-600'; },
    };
}

// ── Range Picker ────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const MONTHS_ID    = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

    const wrap      = document.getElementById('rkpWrap');
    const panel     = document.getElementById('rkpPanel');
    const btn       = document.getElementById('rkpBtn');
    const rangeText = document.getElementById('rkpRangeText');
    const startBox  = document.getElementById('rkpStartBox');
    const endBox    = document.getElementById('rkpEndBox');
    const daysLabel = document.getElementById('rkpDays');
    const startHid  = document.getElementById('rkpStartHidden');
    const endHid    = document.getElementById('rkpEndHidden');
    const gridL     = document.getElementById('rkpGridL');
    const gridR     = document.getElementById('rkpGridR');

    if (!wrap || !panel || !gridL || !gridR) return;

    const pad     = n => String(n).padStart(2, '0');
    const keyOf   = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
    const fmtDisp = d => `${d.getDate()} ${MONTHS_SHORT[d.getMonth()]} ${d.getFullYear()}`;
    const daysDiff = (a, b) => Math.round((new Date(b.getFullYear(),b.getMonth(),b.getDate()) - new Date(a.getFullYear(),a.getMonth(),a.getDate())) / 86400000) + 1;

    const now  = new Date();
    const ago6 = new Date(now); ago6.setDate(now.getDate() - 6);

    let appliedStart = new Date(ago6.getFullYear(), ago6.getMonth(), ago6.getDate());
    let appliedEnd   = new Date(now.getFullYear(),  now.getMonth(),  now.getDate());
    let tempStart    = new Date(appliedStart);
    let tempEnd      = new Date(appliedEnd);
    let picking      = false;
    let viewLeft     = new Date(tempStart.getFullYear(), tempStart.getMonth(), 1);

    const rightView = () => new Date(viewLeft.getFullYear(), viewLeft.getMonth() + 1, 1);

    // Init hidden inputs with default range
    startHid.value  = keyOf(appliedStart);
    endHid.value    = keyOf(appliedEnd);
    rangeText.value = `${keyOf(appliedStart)} — ${keyOf(appliedEnd)}`;

    function positionPanel() {
        const r = wrap.getBoundingClientRect(), winW = window.innerWidth, winH = window.innerHeight;
        const pw = panel.offsetWidth  || 640;
        const ph = panel.offsetHeight || 400;
        let top  = r.bottom + 6, left = r.left;
        if (left + pw > winW - 8) left = winW - pw - 8;
        if (left < 8) left = 8;
        if (top + ph > winH - 8) top = r.top - ph - 6;
        if (top < 8) top = 8;
        panel.style.top = top + 'px'; panel.style.left = left + 'px';
    }

    function setHeaders() {
        const rv = rightView();
        document.getElementById('rkpMonthLabelL').textContent = MONTHS_ID[viewLeft.getMonth()];
        document.getElementById('rkpYearLabelL').textContent  = viewLeft.getFullYear();
        document.getElementById('rkpMonthLabelR').textContent = MONTHS_ID[rv.getMonth()];
        document.getElementById('rkpYearLabelR').textContent  = rv.getFullYear();
    }

    function updateInfo() {
        startBox.textContent  = fmtDisp(tempStart);
        endBox.textContent    = fmtDisp(tempEnd);
        daysLabel.textContent = `${daysDiff(tempStart, tempEnd)} hari`;
        rangeText.value       = `${keyOf(tempStart)} — ${keyOf(tempEnd)}`;
    }

    function isBetween(d, a, b) {
        const t = new Date(d.getFullYear(),d.getMonth(),d.getDate()).getTime();
        return t >= new Date(a.getFullYear(),a.getMonth(),a.getDate()).getTime() &&
               t <= new Date(b.getFullYear(),b.getMonth(),b.getDate()).getTime();
    }

    function renderGrid(grid, view) {
        const y = view.getFullYear(), m = view.getMonth();
        const daysInMonth = new Date(y, m+1, 0).getDate();
        const offset      = (new Date(y, m, 1).getDay() + 6) % 7;
        grid.innerHTML    = '';

        for (let i = 0; i < offset; i++) {
            const e = document.createElement('div'); e.className = 'h-9'; grid.appendChild(e);
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const cur = new Date(y, m, d);
            const isS = keyOf(cur) === keyOf(tempStart), isE = keyOf(cur) === keyOf(tempEnd);
            const isSE = isS && isE, inRg = !isSE && isBetween(cur, tempStart, tempEnd);

            const wrapper = document.createElement('div');
            wrapper.className = 'relative h-9 flex items-center justify-center cursor-pointer';

            if (!isSE && (inRg || isS || isE)) {
                const strip = document.createElement('div');
                strip.className = 'absolute inset-y-0 bg-red-50 pointer-events-none';
                if (isS)      strip.style.cssText = 'left:50%;right:0';
                else if (isE) strip.style.cssText = 'left:0;right:50%';
                else          strip.style.cssText = 'left:0;right:0';
                wrapper.appendChild(strip);
            }

            const circle = document.createElement('div');
            circle.textContent = String(d);
            if (isS || isE) {
                circle.className = 'relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-[11px] bg-[#B40404] text-white font-bold';
            } else if (inRg) {
                circle.className = 'relative z-10 w-8 h-8 flex items-center justify-center text-[11px] text-slate-700 dark:text-slate-300';
            } else {
                circle.className = 'relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-[11px] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1D1D1D]';
            }
            wrapper.appendChild(circle);

            wrapper.addEventListener('click', e => {
                e.stopPropagation();
                const clicked = new Date(y, m, d);
                if (!picking) { tempStart = clicked; tempEnd = clicked; picking = true; }
                else { tempEnd = clicked; if (tempEnd < tempStart) { const t = tempStart; tempStart = tempEnd; tempEnd = t; } picking = false; }
                updateInfo(); render();
            });
            grid.appendChild(wrapper);
        }
    }

    function render() { setHeaders(); updateInfo(); renderGrid(gridL, viewLeft); renderGrid(gridR, rightView()); }

    function closeMenus() {
        ['L','R'].forEach(s => {
            document.getElementById('rkpMonthMenu'+s)?.classList.add('hidden');
            document.getElementById('rkpYearMenu'+s)?.classList.add('hidden');
        });
    }

    function buildMonthMenu(side) {
        const el = document.getElementById('rkpMonthItems'+side); if (!el) return;
        el.innerHTML = '';
        MONTHS_ID.forEach((name, idx) => {
            const b = document.createElement('button'); b.type = 'button';
            b.className = 'w-full text-left px-3 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-[#1D1D1D] text-[12px] text-slate-700 dark:text-slate-300';
            b.textContent = name;
            b.addEventListener('click', () => {
                if (side === 'L') { viewLeft = new Date(viewLeft.getFullYear(), idx, 1); }
                else { const rv = rightView(); viewLeft = new Date(rv.getFullYear(), idx-1, 1); }
                closeMenus(); render();
            });
            el.appendChild(b);
        });
    }

    function buildYearMenu(side) {
        const el = document.getElementById('rkpYearItems'+side); if (!el) return;
        el.innerHTML = '';
        const curY = (side === 'L' ? viewLeft : rightView()).getFullYear();
        for (let y = curY-10; y <= curY+10; y++) {
            const d = document.createElement('div');
            d.textContent = String(y);
            d.className = 'px-3 py-1.5 rounded-lg cursor-pointer hover:bg-slate-100 dark:hover:bg-[#1D1D1D] text-[12px] text-center' + (y===curY ? ' font-bold text-[#B40404]' : ' text-slate-700 dark:text-slate-300');
            d.addEventListener('click', () => {
                if (side === 'L') { viewLeft = new Date(y, viewLeft.getMonth(), 1); }
                else { const rv = rightView(); viewLeft = new Date(y, rv.getMonth()-1, 1); }
                closeMenus(); render();
            });
            el.appendChild(d);
        }
    }

    ['L','R'].forEach(side => {
        const mBtn  = document.getElementById('rkpMonthBtn'+side);
        const yBtn  = document.getElementById('rkpYearBtn'+side);
        const mMenu = document.getElementById('rkpMonthMenu'+side);
        const yMenu = document.getElementById('rkpYearMenu'+side);
        if (mBtn) mBtn.addEventListener('click', e => { e.stopPropagation(); buildMonthMenu(side); mMenu.classList.toggle('hidden'); yMenu.classList.add('hidden'); });
        if (yBtn) yBtn.addEventListener('click', e => { e.stopPropagation(); buildYearMenu(side);  yMenu.classList.toggle('hidden'); mMenu.classList.add('hidden'); });
        if (mMenu) mMenu.addEventListener('click', e => e.stopPropagation());
        if (yMenu) yMenu.addEventListener('click', e => e.stopPropagation());
    });

    document.getElementById('rkpPrev')?.addEventListener('click', () => { viewLeft = new Date(viewLeft.getFullYear(), viewLeft.getMonth()-1, 1); render(); });
    document.getElementById('rkpNext')?.addEventListener('click', () => { viewLeft = new Date(viewLeft.getFullYear(), viewLeft.getMonth()+1, 1); render(); });

    document.getElementById('rkpClear')?.addEventListener('click', () => {
        tempStart = new Date(appliedStart); tempEnd = new Date(appliedEnd);
        picking = false; viewLeft = new Date(tempStart.getFullYear(), tempStart.getMonth(), 1);
        panel.classList.add('hidden');
    });

    document.getElementById('rkpApply')?.addEventListener('click', () => {
        if (!tempStart || !tempEnd) return;
        const diff = daysDiff(tempStart, tempEnd) - 1;
        if (diff > 30) { alert('Rentang tanggal maksimal 31 hari.'); return; }
        appliedStart = new Date(tempStart); appliedEnd = new Date(tempEnd);
        const sd = keyOf(appliedStart), ed = keyOf(appliedEnd);
        startHid.value = sd; endHid.value = ed; rangeText.value = `${sd} — ${ed}`;
        panel.classList.add('hidden');
    });

    btn.addEventListener('click', e => {
        e.stopPropagation();
        if (panel.classList.contains('hidden')) {
            panel.style.visibility = 'hidden'; panel.classList.remove('hidden');
            positionPanel();
            panel.style.visibility = ''; render();
        } else { panel.classList.add('hidden'); }
    });
    rangeText.addEventListener('click', () => btn.click());

    document.addEventListener('click', e => { if (!wrap.contains(e.target) && !panel.contains(e.target)) panel.classList.add('hidden'); });

    // Expose rkpSetRange for reset
    window.rkpSetRange = (sd, ed) => {
        tempStart = new Date(sd + 'T00:00:00'); tempEnd = new Date(ed + 'T00:00:00');
        appliedStart = new Date(tempStart); appliedEnd = new Date(tempEnd);
        startHid.value = sd; endHid.value = ed; rangeText.value = `${sd} — ${ed}`;
        viewLeft = new Date(tempStart.getFullYear(), tempStart.getMonth(), 1);
    };

    render();
});
</script>
@endpush
@endsection
