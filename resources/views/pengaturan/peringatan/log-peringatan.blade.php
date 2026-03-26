{{-- ═══ TAB LOG PERINGATAN ═══ --}}

{{-- Toolbar --}}
<div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
        <h2 class="text-[16px] font-bold text-slate-800">Log Peringatan</h2>
        @php $unread = $alerts->where('is_read', false)->count(); @endphp
        @if($unread > 0)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-600">
                {{ $unread }} Belum Dibaca
            </span>
        @endif
    </div>
    <div class="flex items-center gap-2">
        {{-- Filter Ruangan --}}
        <form method="GET" action="{{ route('pengaturan.peringatan') }}" class="flex items-center gap-2" id="logFilterForm">
            <input type="hidden" name="tab" value="log-peringatan">
            <select name="room_id" onchange="document.getElementById('logFilterForm').submit()"
                class="px-3 py-[7px] border border-slate-200 rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                <option value="">Semua Ruangan</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                @endforeach
            </select>
            <select name="severity" onchange="document.getElementById('logFilterForm').submit()"
                class="px-3 py-[7px] border border-slate-200 rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 bg-white">
                <option value="">Semua Tipe</option>
                <option value="warning"  {{ request('severity') === 'warning'  ? 'selected' : '' }}>Warning</option>
                <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
            </select>
        </form>
        {{-- Tandai Semua Dibaca --}}
        <button onclick="logMarkAllRead()"
            class="flex items-center gap-1.5 px-3 py-[7px] border border-slate-200 text-slate-600 rounded-lg text-[12.5px] hover:bg-slate-50 transition-colors cursor-pointer bg-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Tandai Dibaca
        </button>
        {{-- Hapus Semua --}}
        <button onclick="logClearAll()"
            class="flex items-center gap-1.5 px-3 py-[7px] border border-red-200 text-red-600 rounded-lg text-[12.5px] hover:bg-red-50 transition-colors cursor-pointer bg-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
            Hapus Semua
        </button>
    </div>
</div>

{{-- Table --}}
<div class="overflow-x-auto">
    <table class="w-full text-[13px]">
        <thead>
            <tr class="bg-red-50 text-slate-600 text-left">
                <th class="px-4 py-2.5 font-semibold rounded-l-lg w-8"></th>
                <th class="px-4 py-2.5 font-semibold">Waktu</th>
                <th class="px-4 py-2.5 font-semibold">Ruangan</th>
                <th class="px-4 py-2.5 font-semibold">Tipe</th>
                <th class="px-4 py-2.5 font-semibold">Pesan</th>
                <th class="px-4 py-2.5 font-semibold text-right rounded-r-lg">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($alerts as $alert)
                <tr id="alert-row-{{ $alert->id }}"
                    class="hover:bg-slate-50/60 transition-colors {{ $alert->is_read ? 'opacity-60' : '' }}">

                    {{-- Dot unread --}}
                    <td class="px-4 py-3 text-center">
                        @if(!$alert->is_read)
                            <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                        @endif
                    </td>

                    {{-- Waktu --}}
                    <td class="px-4 py-3 text-slate-500 whitespace-nowrap">
                        <p class="text-[12px]">{{ $alert->created_at->format('d M Y') }}</p>
                        <p class="text-[11px] text-slate-400">{{ $alert->created_at->format('H:i:s') }}</p>
                    </td>

                    {{-- Ruangan --}}
                    <td class="px-4 py-3 font-medium text-slate-700">
                        {{ $alert->room?->name ?? '-' }}
                    </td>

                    {{-- Tipe / Severity badge --}}
                    <td class="px-4 py-3">
                        @if($alert->type === 'sensor_offline')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                📡 Sensor
                            </span>
                        @elseif($alert->type === 'critical' || $alert->alertRule?->severity === 'critical')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-700">
                                🔴 Critical
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-yellow-100 text-yellow-700">
                                ⚠️ Warning
                            </span>
                        @endif
                    </td>

                    {{-- Pesan (hanya baris pertama) --}}
                    <td class="px-4 py-3 text-slate-600 max-w-xs">
                        @php $lines = explode("\n", $alert->message); @endphp
                        <p class="font-medium text-slate-800 text-[12.5px]">{{ $lines[0] ?? '-' }}</p>
                        @if(count($lines) > 1)
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $lines[1] ?? '' }}</p>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Detail (expand) --}}
                            <button title="Detail" onclick="logToggleDetail({{ $alert->id }})"
                                class="text-slate-400 hover:text-slate-700 transition-colors cursor-pointer">
                                <img src="{{ asset('icons/detail.svg') }}" alt="Detail" class="w-6 h-6">
                            </button>
                            {{-- Mark Read --}}
                            @if(!$alert->is_read)
                            <button title="Tandai Dibaca" onclick="logMarkRead({{ $alert->id }})"
                                class="text-slate-400 hover:text-green-600 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                            @endif
                            {{-- Hapus --}}
                            <button title="Hapus" onclick="logDelete({{ $alert->id }})"
                                class="text-slate-400 hover:text-red-600 transition-colors cursor-pointer">
                                <img src="{{ asset('icons/hapus.svg') }}" alt="Hapus" class="w-6 h-6">
                            </button>
                        </div>
                    </td>
                </tr>

                {{-- Detail row (hidden) --}}
                <tr id="alert-detail-{{ $alert->id }}" class="hidden bg-slate-50/80">
                    <td colspan="6" class="px-6 py-4">
                        @if($alert->type === 'sensor_offline')
                            <div class="grid grid-cols-2 gap-3 text-[12.5px]">
                                <div>
                                    <span class="text-slate-400 text-[11px] uppercase tracking-wide font-semibold">Nama Peringatan</span>
                                    <p class="font-semibold text-slate-800 mt-0.5">Sensor Offline</p>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[11px] uppercase tracking-wide font-semibold">Kategori</span>
                                    <p class="font-semibold text-slate-800 mt-0.5">Sensor</p>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[11px] uppercase tracking-wide font-semibold">Ruangan</span>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ $alert->room?->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[11px] uppercase tracking-wide font-semibold">Waktu Terdeteksi</span>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ $alert->created_at->format('d M Y, H:i:s') }}</p>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-slate-400 text-[11px] uppercase tracking-wide font-semibold">Keterangan</span>
                                    <p class="text-slate-700 mt-0.5">{{ $alert->message }}</p>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-3 text-[12.5px]">
                                <div>
                                    <span class="text-slate-400 text-[11px] uppercase tracking-wide font-semibold">Nama Peringatan</span>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ $alert->alertRule?->name ?? ucfirst(str_replace('_', ' ', $alert->type)) }}</p>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[11px] uppercase tracking-wide font-semibold">Kategori</span>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ $alert->alertRule?->kategori ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[11px] uppercase tracking-wide font-semibold">Ruangan</span>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ $alert->room?->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[11px] uppercase tracking-wide font-semibold">Nilai Terukur</span>
                                    <p class="font-semibold text-slate-800 mt-0.5">{{ $alert->nilai !== null ? $alert->nilai : '-' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-slate-400 text-[11px] uppercase tracking-wide font-semibold">Keterangan</span>
                                    <p class="text-slate-700 mt-0.5">{{ $alert->message }}</p>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-14 text-center">
                        <div class="flex flex-col items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24" class="mb-2 opacity-40">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <p class="text-[13px]">Tidak ada log peringatan</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($alerts instanceof \Illuminate\Pagination\LengthAwarePaginator && $alerts->hasPages())
<div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-50">
    <span class="text-[12px] text-slate-400">
        Menampilkan {{ $alerts->firstItem() }} – {{ $alerts->lastItem() }} dari {{ $alerts->total() }} log
    </span>
    <div class="flex items-center gap-1">
        @if(!$alerts->onFirstPage())
            <a href="{{ $alerts->previousPageUrl() }}&tab=log-peringatan"
               class="w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:bg-slate-100 no-underline">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
        @endif
        @foreach($alerts->getUrlRange(1, $alerts->lastPage()) as $page => $url)
            <a href="{{ $url }}&tab=log-peringatan"
               class="w-7 h-7 flex items-center justify-center rounded text-[12px] font-medium no-underline {{ $page == $alerts->currentPage() ? 'bg-red-700 text-white' : 'text-slate-500 hover:bg-slate-100' }}">
                {{ $page }}
            </a>
        @endforeach
        @if($alerts->hasMorePages())
            <a href="{{ $alerts->nextPageUrl() }}&tab=log-peringatan"
               class="w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:bg-slate-100 no-underline">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        @endif
    </div>
</div>
@endif

@push('modals')
<script>
const LOG_CSRF = '{{ csrf_token() }}';
const LOG_ROUTES = {
    read:     '{{ url('pengaturan/peringatan/log') }}/__ID__/read',
    readAll:  '{{ route('pengaturan.peringatan.log.read-all') }}',
    destroy:  '{{ url('pengaturan/peringatan/log') }}/__ID__',
    clear:    '{{ route('pengaturan.peringatan.log.clear') }}',
};

function logToggleDetail(id) {
    const row = document.getElementById('alert-detail-' + id);
    row.classList.toggle('hidden');
}

function logMarkRead(id) {
    fetch(LOG_ROUTES.read.replace('__ID__', id), {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': LOG_CSRF, 'Accept': 'application/json' },
    }).then(() => {
        const row = document.getElementById('alert-row-' + id);
        if (row) row.classList.add('opacity-60');
        // Hapus tombol centang & dot
        row.querySelector('[title="Tandai Dibaca"]')?.remove();
        row.querySelector('.bg-red-500')?.remove();
    });
}

function logMarkAllRead() {
    if (!confirm('Tandai semua log sebagai sudah dibaca?')) return;
    fetch(LOG_ROUTES.readAll, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': LOG_CSRF, 'Accept': 'application/json' },
    }).then(() => location.reload());
}

function logDelete(id) {
    if (!confirm('Hapus log peringatan ini?')) return;
    fetch(LOG_ROUTES.destroy.replace('__ID__', id), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': LOG_CSRF, 'Accept': 'application/json' },
    }).then(() => {
        document.getElementById('alert-row-' + id)?.remove();
        document.getElementById('alert-detail-' + id)?.remove();
    });
}

function logClearAll() {
    if (!confirm('Hapus SEMUA log peringatan? Tindakan ini tidak dapat dibatalkan.')) return;
    fetch(LOG_ROUTES.clear, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': LOG_CSRF, 'Accept': 'application/json' },
    }).then(() => location.reload());
}
</script>
@endpush
