{{-- ═══ TAB RUANGAN ═══ --}}

<div class="flex items-center justify-between mb-5">
    <h2 class="text-[16px] font-bold text-slate-800">Daftar Ruangan</h2>
    <div class="flex items-center gap-3">
        {{-- Search --}}
        <form method="GET" action="{{ route('pengaturan.konfigurasi') }}" class="flex items-center">
            <input type="hidden" name="tab" value="ruangan">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari ..."
                    class="pl-8 pr-3 py-[7px] border border-slate-200 rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 w-44">
            </div>
        </form>
        {{-- Tambah Ruangan --}}
        <button onclick="openRoomModal()" class="flex items-center gap-1.5 bg-red-700 hover:bg-red-800 text-white text-[12.5px] font-semibold px-4 py-[8px] rounded-lg transition-colors cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Ruangan
        </button>
    </div>
</div>

{{-- Table --}}
<div class="overflow-x-auto">
    <table class="w-full text-[13px]">
        <thead>
            <tr class="bg-red-50 text-slate-600 text-left">
                <th class="px-4 py-2.5 font-semibold rounded-l-lg">Nama Ruangan</th>
                <th class="px-4 py-2.5 font-semibold">Kode Ruangan</th>
                <th class="px-4 py-2.5 font-semibold">Status Monitoring</th>
                <th class="px-4 py-2.5 font-semibold">Urutan Denah</th>
                <th class="px-4 py-2.5 font-semibold">Status Ruangan</th>
                <th class="px-4 py-2.5 font-semibold text-center rounded-r-lg">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($rooms as $room)
                @php
                    $hasMonitoring = $room->sensors->count() > 0;
                    $isActive      = $room->is_active;
                @endphp
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-4 py-2.5 text-slate-800 font-medium">{{ $room->name }}</td>
                    <td class="px-4 py-2.5 text-slate-500">{{ $room->code ?? '-' }}</td>
                    <td class="px-4 py-2.5">
                        @if($hasMonitoring)
                            <span class="inline-flex items-center gap-1.5 text-green-600 font-medium">
                                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                Termonitor
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-slate-400 font-medium">
                                <span class="w-2 h-2 rounded-full bg-slate-400 inline-block"></span>
                                Belum Dimonitor
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-slate-500">{{ $room->sort_order ?: '-' }}</td>
                    <td class="px-4 py-2.5">
                        @if($isActive)
                            <span class="text-green-600 font-semibold text-[12px]">Aktif</span>
                        @else
                            <span class="text-slate-400 font-semibold text-[12px]">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Detail --}}
                            <button title="Detail" class="text-slate-400 hover:text-slate-700 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                            </button>
                            {{-- Edit --}}
                            <button title="Edit"
                                onclick="openRoomModal({{ $room->id }}, '{{ addslashes($room->name) }}', '{{ addslashes($room->code) }}', {{ $room->sort_order }}, {{ $room->is_active ? 1 : 0 }}, {{ $room->sensors->count() > 0 ? 1 : 0 }})"
                                class="text-slate-400 hover:text-blue-600 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            {{-- Hapus --}}
                            <button title="Hapus"
                                onclick="deleteRoom({{ $room->id }}, '{{ addslashes($room->name) }}')"
                                class="text-slate-400 hover:text-red-600 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-[13px]">Tidak ada data ruangan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-50">
    <span class="text-[12px] text-slate-400">
        Menampilkan {{ $rooms->firstItem() ?? 0 }} – {{ $rooms->lastItem() ?? 0 }} dari {{ $rooms->total() }} data
    </span>
    <div class="flex items-center gap-1">
        @if($rooms->onFirstPage())
            <span class="w-7 h-7 flex items-center justify-center rounded text-slate-300 cursor-not-allowed">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </span>
        @else
            <a href="{{ $rooms->previousPageUrl() }}&tab=ruangan&search={{ $search }}"
                class="w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:bg-slate-100 no-underline transition-colors">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
        @endif

        @foreach($rooms->getUrlRange(1, $rooms->lastPage()) as $page => $url)
            <a href="{{ $url }}&tab=ruangan&search={{ $search }}"
                class="w-7 h-7 flex items-center justify-center rounded text-[12px] font-medium no-underline transition-colors
                    {{ $page == $rooms->currentPage()
                        ? 'bg-red-700 text-white'
                        : 'text-slate-500 hover:bg-slate-100' }}">
                {{ $page }}
            </a>
        @endforeach

        @if($rooms->hasMorePages())
            <a href="{{ $rooms->nextPageUrl() }}&tab=ruangan&search={{ $search }}"
                class="w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:bg-slate-100 no-underline transition-colors">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        @else
            <span class="w-7 h-7 flex items-center justify-center rounded text-slate-300 cursor-not-allowed">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
        @endif
    </div>
</div>

{{-- ═══ MODAL TAMBAH / EDIT RUANGAN ═══ --}}
@push('modals')
<div id="roomModal" class="hidden fixed inset-0 bg-black/40 z-[1000] items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-[480px] max-w-[95vw] overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="text-[15px] font-bold text-slate-800" id="roomModalTitle">Tambah Ruangan</h3>
            <button onclick="closeRoomModal()" class="text-slate-400 hover:text-slate-700 transition-colors cursor-pointer bg-transparent border-none p-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 flex flex-col gap-4">
            <input type="hidden" id="roomModalId">

            {{-- Nama Ruangan --}}
            <div>
                <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5">Nama Ruangan <span class="text-red-500">*</span></label>
                <input type="text" id="roomModalName" placeholder="Contoh: Ruang Rapat"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-red-400 box-border transition-colors">
            </div>

            {{-- Kode & Urutan Denah --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5">Kode Ruangan <span class="text-red-500">*</span></label>
                    <input type="text" id="roomModalCode" placeholder="RM-01"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-red-400 box-border uppercase transition-colors">
                </div>
                <div>
                    <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5">Urutan Denah</label>
                    <input type="number" id="roomModalOrder" placeholder="0" min="0"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-red-400 box-border transition-colors">
                    <p class="text-[10.5px] text-slate-400 mt-1">Urutan tampil di denah. 0 = otomatis.</p>
                </div>
            </div>

            {{-- Status Monitoring (read-only info) --}}
            <div class="bg-slate-50 rounded-lg px-4 py-3 flex items-center justify-between">
                <div>
                    <p class="text-[11.5px] font-semibold text-slate-500">Status Monitoring</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Otomatis dari jumlah sensor yang terhubung</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-slate-400 text-[12px] font-medium" id="roomModalMonitoringBadge">
                    <span class="w-2 h-2 rounded-full bg-slate-300 inline-block"></span>
                    Belum Dimonitor
                </span>
            </div>

            {{-- Status Aktif --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11.5px] font-semibold text-slate-700">Status Ruangan</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Aktifkan untuk menampilkan ruangan di sistem</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="roomModalActive" class="sr-only peer" checked>
                    <div class="w-10 h-5 bg-slate-200 rounded-full peer
                                peer-checked:bg-red-600 transition-colors
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                    <span class="ml-2.5 text-[12px] font-semibold" id="roomModalActiveLabel">Aktif</span>
                </label>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-2 px-6 py-4 border-t border-slate-100 bg-slate-50/60">
            <button onclick="closeRoomModal()"
                class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-[13px] hover:bg-slate-100 transition-colors cursor-pointer bg-white">
                Batal
            </button>
            <button onclick="saveRoom()"
                class="px-5 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg text-[13px] font-semibold transition-colors cursor-pointer border-none">
                <span id="roomModalSaveBtnLabel">Simpan</span>
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="konfig-toast" class="fixed bottom-6 right-6 bg-slate-800 text-white px-[18px] py-2.5 rounded-xl text-[13px] z-[9999] hidden shadow-xl"></div>

<script>
const KONFIG_ROUTES = {
    store:   '{{ route('pengaturan.rooms.store') }}',
    update:  '{{ route('pengaturan.rooms.update', ['room' => '__ID__']) }}',
    destroy: '{{ route('pengaturan.rooms.destroy', ['room' => '__ID__']) }}',
};
const KONFIG_CSRF = '{{ csrf_token() }}';

let _editRoomId = null;

function openRoomModal(id = null, name = '', code = '', sortOrder = 0, isActive = 1, hasMonitoring = 0) {
    _editRoomId = id;
    document.getElementById('roomModalId').value          = id || '';
    document.getElementById('roomModalName').value        = name;
    document.getElementById('roomModalCode').value        = code.toUpperCase();
    document.getElementById('roomModalOrder').value       = sortOrder;
    document.getElementById('roomModalActive').checked    = isActive == 1;
    document.getElementById('roomModalActiveLabel').textContent = isActive == 1 ? 'Aktif' : 'Nonaktif';
    document.getElementById('roomModalTitle').textContent = id ? 'Edit Ruangan' : 'Tambah Ruangan';
    document.getElementById('roomModalSaveBtnLabel').textContent = 'Simpan';

    const badge = document.getElementById('roomModalMonitoringBadge');
    if (!id) {
        // Mode tambah: belum ada sensor
        badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-slate-300 inline-block"></span> Belum Dimonitor`;
        badge.className = 'inline-flex items-center gap-1.5 text-slate-400 text-[12px] font-medium';
    } else if (hasMonitoring) {
        badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Termonitor`;
        badge.className = 'inline-flex items-center gap-1.5 text-green-600 text-[12px] font-medium';
    } else {
        badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-slate-400 inline-block"></span> Belum Dimonitor`;
        badge.className = 'inline-flex items-center gap-1.5 text-slate-400 text-[12px] font-medium';
    }

    const modal = document.getElementById('roomModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => document.getElementById('roomModalName').focus(), 50);
}

function closeRoomModal() {
    const modal = document.getElementById('roomModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    _editRoomId = null;
}

function saveRoom() {
    const name      = document.getElementById('roomModalName').value.trim();
    const code      = document.getElementById('roomModalCode').value.trim().toUpperCase();
    const sortOrder = parseInt(document.getElementById('roomModalOrder').value) || 0;
    const isActive  = document.getElementById('roomModalActive').checked ? 1 : 0;

    if (!name || !code) { showKonfigToast('Nama dan Kode ruangan wajib diisi.'); return; }

    document.getElementById('roomModalSaveBtnLabel').textContent = 'Menyimpan...';

    const isEdit = !!_editRoomId;
    const url    = isEdit ? KONFIG_ROUTES.update.replace('__ID__', _editRoomId) : KONFIG_ROUTES.store;
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': KONFIG_CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ name, code, sort_order: sortOrder, is_active: isActive }),
    })
    .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
    .then(({ ok, data }) => {
        if (!ok) {
            document.getElementById('roomModalSaveBtnLabel').textContent = 'Simpan';
            const msgs = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Gagal menyimpan.');
            showKonfigToast('Error: ' + msgs);
            return;
        }
        closeRoomModal();
        showKonfigToast(isEdit ? 'Ruangan berhasil diperbarui ✓' : 'Ruangan berhasil ditambahkan ✓');
        setTimeout(() => location.reload(), 900);
    })
    .catch(() => {
        document.getElementById('roomModalSaveBtnLabel').textContent = 'Simpan';
        showKonfigToast('Terjadi kesalahan jaringan.');
    });
}

function deleteRoom(id, name) {
    if (!confirm('Hapus ruangan "' + name + '"? Tindakan ini tidak dapat dibatalkan.')) return;
    fetch(KONFIG_ROUTES.destroy.replace('__ID__', id), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': KONFIG_CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showKonfigToast('Ruangan dihapus ✓');
            setTimeout(() => location.reload(), 900);
        }
    })
    .catch(() => showKonfigToast('Gagal menghapus ruangan.'));
}

document.getElementById('roomModalActive').addEventListener('change', function() {
    document.getElementById('roomModalActiveLabel').textContent = this.checked ? 'Aktif' : 'Nonaktif';
});
document.getElementById('roomModal').addEventListener('click', function(e) {
    if (e.target === this) closeRoomModal();
});

function showKonfigToast(msg) {
    const t = document.getElementById('konfig-toast');
    t.textContent = msg;
    t.classList.remove('hidden');
    clearTimeout(t._tmr);
    t._tmr = setTimeout(() => t.classList.add('hidden'), 2800);
}
</script>
@endpush
