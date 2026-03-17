{{-- Tab: Batas Normal --}}
<form id="form-batas-normal">
@csrf

@foreach($limits as $key => $limit)
    <input type="hidden" name="limits[{{ $loop->index }}][parameter_key]" value="{{ $key }}">
@endforeach

{{-- 2-column sensor card grid --}}
<div class="grid grid-cols-2 gap-5 mb-6">
    @foreach($limits as $key => $limit)
    @php
        $i       = $loop->index;
        $isBound = in_array($key, ['co2', 'daya']); // single-bound (< normal_max, > poor_high)
    @endphp
    <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-5">

        {{-- Card header --}}
        <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-slate-200/80">
            @if($limit->icon_type === 'img')
                <img src="{{ asset($limit->icon) }}" alt="{{ $limit->label }}" class="w-7 h-7 shrink-0">
            @else
                <span class="text-xl leading-none">{{ $limit->icon }}</span>
            @endif
            <h3 class="text-[16px] font-semibold text-slate-800">{{ $limit->label }}</h3>
        </div>

        {{-- Rows --}}
        <div class="space-y-2.5">

            {{-- ── Normal ── --}}
            <div class="flex items-center gap-2">
                <span class="flex items-center gap-1.5 w-[72px] shrink-0">
                    <span class="w-[9px] h-[9px] rounded-full bg-green-500 shrink-0"></span>
                    <span class="text-[12.5px] text-slate-500 font-medium">Normal</span>
                </span>

                @if($isBound)
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-slate-800 font-medium select-none">&lt;</span>
                        <input type="number" step="any"
                            name="limits[{{ $i }}][normal_max]"
                            value="{{ old("limits.{$i}.normal_max", $limit->normal_max) }}"
                            class="w-full border border-slate-200 bg-white rounded-lg pl-7 pr-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                    </div>
                @else
                    <input type="number" step="any"
                        name="limits[{{ $i }}][normal_min]"
                        value="{{ old("limits.{$i}.normal_min", $limit->normal_min) }}"
                        class="flex-1 border border-slate-200 bg-white rounded-lg px-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                    <span class="text-[13px] text-slate-800 shrink-0">-</span>
                    <input type="number" step="any"
                        name="limits[{{ $i }}][normal_max]"
                        value="{{ old("limits.{$i}.normal_max", $limit->normal_max) }}"
                        class="flex-1 border border-slate-200 bg-white rounded-lg px-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                @endif
            </div>

            {{-- ── Warning ── --}}
            <div class="flex items-center gap-2">
                <span class="flex items-center gap-1.5 w-[72px] shrink-0">
                    <span class="w-[9px] h-[9px] rounded-full bg-orange-400 shrink-0"></span>
                    <span class="text-[12.5px] text-slate-500 font-medium">Warning</span>
                </span>

                @if($isBound)
                    {{-- e.g. CO2: 800 - 1000 (single high band) --}}
                    <input type="number" step="any"
                        name="limits[{{ $i }}][warn_high_min]"
                        value="{{ old("limits.{$i}.warn_high_min", $limit->warn_high_min) }}"
                        class="flex-1 border border-slate-200 bg-white rounded-lg px-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                    <span class="text-[13px] text-slate-800 shrink-0">-</span>
                    <input type="number" step="any"
                        name="limits[{{ $i }}][warn_high_max]"
                        value="{{ old("limits.{$i}.warn_high_max", $limit->warn_high_max) }}"
                        class="flex-1 border border-slate-200 bg-white rounded-lg px-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                @else
                    {{-- e.g. Suhu: 21-23 / 26-28 — each pair grouped as flex-1 like Normal --}}
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <input type="number" step="any"
                            name="limits[{{ $i }}][warn_low_min]"
                            value="{{ old("limits.{$i}.warn_low_min", $limit->warn_low_min) }}"
                            class="flex-1 min-w-0 border border-slate-200 bg-white rounded-lg px-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                        <span class="text-[13px] text-slate-800 shrink-0">-</span>
                        <input type="number" step="any"
                            name="limits[{{ $i }}][warn_low_max]"
                            value="{{ old("limits.{$i}.warn_low_max", $limit->warn_low_max) }}"
                            class="flex-1 min-w-0 border border-slate-200 bg-white rounded-lg px-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                    </div>
                    <span class="text-[12px] text-slate-800 shrink-0 font-light px-0.5">/</span>
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <input type="number" step="any"
                            name="limits[{{ $i }}][warn_high_min]"
                            value="{{ old("limits.{$i}.warn_high_min", $limit->warn_high_min) }}"
                            class="flex-1 min-w-0 border border-slate-200 bg-white rounded-lg px-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                        <span class="text-[13px] text-slate-800 shrink-0">-</span>
                        <input type="number" step="any"
                            name="limits[{{ $i }}][warn_high_max]"
                            value="{{ old("limits.{$i}.warn_high_max", $limit->warn_high_max) }}"
                            class="flex-1 min-w-0 border border-slate-200 bg-white rounded-lg px-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                    </div>
                @endif
            </div>

            {{-- ── Poor ── --}}
            <div class="flex items-center gap-2">
                <span class="flex items-center gap-1.5 w-[72px] shrink-0">
                    <span class="w-[9px] h-[9px] rounded-full bg-red-500 shrink-0"></span>
                    <span class="text-[12.5px] text-slate-500 font-medium">Poor</span>
                </span>

                @if($isBound)
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-slate-800 font-medium select-none">&gt;</span>
                        <input type="number" step="any"
                            name="limits[{{ $i }}][poor_high]"
                            value="{{ old("limits.{$i}.poor_high", $limit->poor_high) }}"
                            class="w-full border border-slate-200 bg-white rounded-lg pl-7 pr-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                    </div>
                @else
                    {{-- e.g. < 21 / > 28 --}}
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-slate-800 font-medium select-none">&lt;</span>
                        <input type="number" step="any"
                            name="limits[{{ $i }}][poor_low]"
                            value="{{ old("limits.{$i}.poor_low", $limit->poor_low) }}"
                            class="w-full border border-slate-200 bg-white rounded-lg pl-7 pr-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                    </div>
                    <span class="text-[12px] text-slate-800 shrink-0 font-light px-0.5">/</span>
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-slate-800 font-medium select-none">&gt;</span>
                        <input type="number" step="any"
                            name="limits[{{ $i }}][poor_high]"
                            value="{{ old("limits.{$i}.poor_high", $limit->poor_high) }}"
                            class="w-full border border-slate-200 bg-white rounded-lg pl-7 pr-3 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors">
                    </div>
                @endif
            </div>

        </div>
    </div>
    @endforeach
</div>

{{-- Action Buttons — inside the card, bottom-right --}}
<div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
    <button type="button" id="btn-reset-batas"
            class="px-6 py-2.5 rounded-lg border border-slate-300 text-[13px] font-medium text-slate-600 hover:bg-slate-50 transition-colors">
        Reset
    </button>
    <button type="submit"
            class="px-7 py-2.5 rounded-lg bg-red-700 text-white text-[13px] font-semibold hover:bg-red-800 transition-colors shadow-sm">
        Simpan
    </button>
</div>

</form>

{{-- Toast --}}
<div id="toast-batas" class="hidden fixed bottom-6 right-6 z-50 text-white text-[13px] font-medium px-5 py-3 rounded-xl shadow-lg flex items-center gap-2.5"></div>

@push('scripts')
<script>
(function () {
    const form     = document.getElementById('form-batas-normal');
    const btnReset = document.getElementById('btn-reset-batas');
    const toast    = document.getElementById('toast-batas');

    function showToast(msg, ok = true) {
        toast.textContent = msg;
        toast.className = 'fixed bottom-6 right-6 z-50 text-white text-[13px] font-medium px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 '
                        + (ok ? 'bg-green-600' : 'bg-red-600');
        setTimeout(() => { toast.className += ' hidden'; }, 3000);
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const res  = await fetch('{{ route('pengaturan.peringatan.batas-normal.save') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        });
        const json = await res.json();
        showToast(json.success ? 'Batas normal berhasil disimpan.' : 'Gagal menyimpan.', json.success);
    });

    btnReset.addEventListener('click', async () => {
        if (!confirm('Reset semua batas ke nilai kosong?')) return;
        const res  = await fetch('{{ route('pengaturan.peringatan.batas-normal.reset') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json',
            },
        });
        const json = await res.json();
        if (json.success) location.reload();
    });
})();
</script>
@endpush
