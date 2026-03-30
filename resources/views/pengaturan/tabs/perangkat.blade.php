{{-- ═══ TAB PERANGKAT ═══ --}}

<div class="flex items-center justify-between mb-5">
    <h2 class="text-[16px] font-bold text-slate-800 dark:text-white">Daftar Perangkat</h2>
    <div class="flex items-center gap-3">
        {{-- Search --}}
        <form method="GET" action="{{ route('pengaturan.konfigurasi') }}" class="flex items-center">
            <input type="hidden" name="tab" value="perangkat">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari perangkat / ruangan..."
                    class="pl-8 pr-3 py-[7px] border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 w-52">
            </div>
        </form>
        {{-- Tambah Perangkat --}}
        <button type="button" onclick="openAcModal()"
            class="flex items-center gap-1.5 bg-red-700 hover:bg-red-800 text-white text-[12.5px] font-semibold px-4 py-[7px] rounded-lg transition-colors cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Perangkat
        </button>
    </div>
</div>

{{-- Table --}}
<div class="overflow-x-auto">
    <table class="w-full text-[13px]">
        <thead>
            <tr class="bg-red-100 text-slate-800 text-left dark:bg-[#1D1D1D] dark:text-white">
                <th class="px-4 py-2.5 font-semibold rounded-l-lg">Nama Perangkat</th>
                <th class="px-4 py-2.5 font-semibold">Jenis</th>
                <th class="px-4 py-2.5 font-semibold">Ruangan</th>
                <th class="px-4 py-2.5 font-semibold">Monitoring</th>
                <th class="px-4 py-2.5 font-semibold">Status Perangkat</th>
                <th class="px-4 py-2.5 font-semibold text-center rounded-r-lg">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 dark:divide-[#1D1D1D]">
            @forelse($acUnits as $unit)
                <tr class="hover:bg-slate-50/60 dark:hover:bg-transparent transition-colors">

                    {{-- Nama Perangkat --}}
                    <td class="px-4 py-2.5">
                        <p class="font-medium text-slate-800 dark:text-slate-200">{{ $unit->name }}</p>
                        @if($unit->power_kw > 0)
                            <p class="text-[11px] text-slate-400 mt-0.5 dark:text-slate-200">{{ $unit->power_kw }} kW</p>
                        @endif
                    </td>

                    {{-- Jenis --}}
                    <td class="px-4 py-2.5 text-slate-600 dark:text-slate-200">AC</td>

                    {{-- Ruangan --}}
                    <td class="px-4 py-2.5 text-slate-600 dark:text-slate-200">
                        {{ $unit->room?->name ?? '-' }}
                    </td>

                    {{-- Monitoring (linked to room is_active) --}}
                    <td class="px-4 py-2.5">
                        @if($unit->room?->is_active)
                            <span class="inline-flex items-center gap-1.5 text-green-600 font-medium text-[12.5px]">
                                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-slate-400 font-medium text-[12.5px]">
                                <span class="w-2 h-2 rounded-full bg-slate-400 inline-block"></span>
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    {{-- Status Perangkat: ON / OFF badge --}}
                    <td class="px-4 py-2.5">
                        @if($unit->is_active)
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11.5px] font-bold bg-green-500 text-white">
                                ON
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11.5px] font-bold bg-slate-700 text-white">
                                OFF
                            </span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-2.5">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Detail --}}
                            <button title="Detail"
                                onclick="openAcDetail(
                                    '{{ addslashes($unit->name) }}',
                                    {{ $unit->room_id }},
                                    '{{ addslashes($unit->room?->name ?? '-') }}',
                                    {{ $unit->power_kw }},
                                    {{ $unit->is_active ? 1 : 0 }},
                                    {{ $unit->room?->is_active ? 1 : 0 }},
                                    '{{ $unit->updated_at?->format('d M Y, H:i') ?? '-' }}'
                                )"
                                class="text-slate-400 hover:text-slate-700 transition-colors cursor-pointer">
                                <img src="{{ asset('icons/detail.svg') }}" alt="Detail" class="w-7 h-7">
                            </button>
                            {{-- Edit --}}
                            <button title="Edit"
                                onclick="openAcModal({{ $unit->id }}, '{{ addslashes($unit->name) }}', {{ $unit->room_id }}, {{ $unit->power_kw }}, {{ $unit->is_active ? 1 : 0 }})"
                                class="text-slate-400 hover:text-blue-600 transition-colors cursor-pointer">
                                <img src="{{ asset('icons/edit.svg') }}" alt="Edit" class="w-7 h-7">
                            </button>
                            {{-- Hapus --}}
                            <button title="Hapus"
                                onclick="deleteAcUnit({{ $unit->id }}, '{{ addslashes($unit->name) }}')"
                                class="text-slate-400 hover:text-red-600 transition-colors cursor-pointer">
                                <img src="{{ asset('icons/hapus.svg') }}" alt="Hapus" class="w-7 h-7">
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" stroke="currentColor"
                                 stroke-width="1.5" viewBox="0 0 24 24" class="mb-2 opacity-40">
                                <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                            </svg>
                            <p class="text-[13px]">Tidak ada data perangkat</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($acUnits instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-50 dark:border-[#1D1D1D]">
    <span class="text-[12px] text-slate-400">
        Menampilkan {{ $acUnits->firstItem() ?? 0 }} – {{ $acUnits->lastItem() ?? 0 }} dari {{ $acUnits->total() }} data
    </span>
    <div class="flex items-center gap-1">
        @if($acUnits->onFirstPage())
            <span class="w-7 h-7 flex items-center justify-center rounded text-slate-300 cursor-not-allowed">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </span>
        @else
            <a href="{{ $acUnits->previousPageUrl() }}&tab=perangkat&search={{ $search }}"
               class="w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:bg-slate-100 no-underline transition-colors">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
        @endif

        @foreach($acUnits->getUrlRange(1, $acUnits->lastPage()) as $page => $url)
            <a href="{{ $url }}&tab=perangkat&search={{ $search }}"
               class="w-7 h-7 flex items-center justify-center rounded text-[12px] font-medium no-underline transition-colors
                   {{ $page == $acUnits->currentPage() ? 'bg-red-700 text-white dark:border-[#FDEBEB] dark:bg-[#FDEBEB] dark:text-black' : 'text-slate-500 hover:bg-slate-100' }}">
                {{ $page }}
            </a>
        @endforeach

        @if($acUnits->hasMorePages())
            <a href="{{ $acUnits->nextPageUrl() }}&tab=perangkat&search={{ $search }}"
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
@endif

{{-- ═══ MODAL TAMBAH / EDIT PERANGKAT ═══ --}}
@push('modals')

{{-- ── Detail Perangkat Modal ────────────────────────────────────────────── --}}
<div id="acDetailModal" class="hidden fixed inset-0 bg-black/40 z-[1001] items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-[440px] max-w-[95vw] mx-auto overflow-hidden relative">

        {{-- Close --}}
        <button onclick="closeAcDetail()"
            class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors cursor-pointer border-none bg-transparent z-10">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        {{-- Header --}}
        <div class="px-7 pt-6 pb-4 border-b border-slate-100">
            <h3 class="text-[18px] font-bold text-slate-800">Detail Perangkat</h3>
        </div>

        {{-- Body --}}
        <div class="px-7 py-5 flex flex-col gap-4">

            {{-- Row 1: Nama Perangkat + Jenis --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-1">Nama Perangkat</p>
                    <p id="acdName" class="text-[13px] font-bold text-slate-800">—</p>
                </div>
                <div>
                    <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-1">Jenis</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-[12px] font-semibold">AC</span>
                </div>
                <div>
                    <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-1">Ruangan</p>
                    <p id="acdRoom" class="text-[13px] font-semibold text-slate-700">—</p>
                </div>
            </div>

            {{-- Row 2: Ruangan + Monitoring --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-1">Monitoring</p>
                    <div id="acdMonitoring">—</div>
                </div>
                <div>
                    <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-1">Status Perangkat</p>
                    <div id="acdStatus">—</div>
                </div>
                <div>
                    <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-1">Update Terakhir</p>
                    <p id="acdUpdated" class="text-[12px] text-slate-800">—</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="acModal" class="hidden fixed inset-0 bg-black/40 z-[1000] items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-[460px] max-w-[95vw] overflow-hidden dark:bg-[#232323] dark:border-[#232323] dark:text-slate-200">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-[#1D1D1D]">
            <h3 class="text-[15px] font-bold text-slate-800 dark:text-slate-200" id="acModalTitle">Tambah Perangkat</h3>
            <button onclick="closeAcModal()" class="text-slate-400 hover:text-slate-700 transition-colors cursor-pointer bg-transparent border-none p-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 flex flex-col gap-4">
            <input type="hidden" id="acModalId">

            {{-- Nama Perangkat --}}
            <div>
                <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5 dark:text-slate-200">Nama Perangkat <span class="text-red-500">*</span></label>
                <input type="text" id="acModalName" placeholder="Contoh: AC Software"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-red-400 box-border transition-colors dark:bg-[#3C3D3F] dark:border-[#3C3D3F] dark:text-slate-200">
            </div>

            {{-- Ruangan & Daya --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5 dark:text-slate-200">Ruangan <span class="text-red-500">*</span></label>
                    <div class="relative custom-select-wrapper">
                        <select id="acModalRoomId" class="hidden real-select">
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($allRooms as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-2 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer">
                            <span class="select-text truncate text-left">-- Pilih Ruangan --</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-[1100] text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                </div>
                <div>
                    <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5 dark:text-slate-200">Daya (kW)</label>
                    <input type="number" id="acModalPower" placeholder="0.00" min="0" step="0.01"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-red-400 box-border transition-colors dark:bg-[#3C3D3F] dark:border-[#3C3D3F] dark:text-slate-200">
                </div>
            </div>

            {{-- Status Perangkat --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11.5px] font-semibold text-slate-700 dark:text-slate-200">Status Perangkat</p>
                    <p class="text-[11px] text-slate-400 mt-0.5 dark:text-slate-200">Aktifkan / matikan perangkat</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="acModalActive" class="sr-only peer">
                    <div class="w-10 h-5 bg-slate-200 rounded-full peer
                                peer-checked:bg-green-500 transition-colors
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                    <span class="ml-2.5 text-[12px] font-bold" id="acModalActiveLabel">OFF</span>
                </label>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-2 px-6 py-4 border-t border-slate-100 bg-slate-50/60 dark:bg-[#232323] dark:border-[#232323] dark:text-slate-200">
            <button onclick="closeAcModal()"
                class="px-4 py-2 border border-slate-200 text-slate-600 dark:border-[#FFFFFF] dark:text-[#FFFFFF] dark:bg-transparent rounded-lg text-[13px] hover:bg-slate-100 transition-colors cursor-pointer bg-white">
                Batal
            </button>
            <button onclick="saveAcUnit()"
                class="px-5 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg text-[13px] font-semibold transition-colors cursor-pointer border-none">
                <span id="acModalSaveBtn">Simpan</span>
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="ac-toast" class="fixed bottom-6 right-6 bg-slate-800 text-white px-[18px] py-2.5 rounded-xl text-[13px] z-[9999] hidden shadow-xl"></div>

{{-- Modal Konfirmasi Hapus Perangkat --}}
<div id="modal-delete-ac" class="hidden fixed inset-0 bg-black/50 z-[1100] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#232323] rounded-2xl shadow-2xl w-full max-w-sm text-center overflow-hidden">
        <div class="px-8 pt-8 pb-6">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('icons/delete.svg') }}" alt="Hapus" class="w-12 h-12">
            </div>
            <h3 class="text-[16px] font-bold text-slate-800 dark:text-white mb-2">Hapus Perangkat</h3>
            <p class="text-[13px] text-slate-500 dark:text-slate-400">
                Anda yakin ingin menghapus perangkat <strong id="delete-ac-name" class="text-slate-700 dark:text-slate-200"></strong>?
                <br>Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div class="flex justify-center gap-3 px-8 pb-7">
            <button onclick="closeDeleteAcModal()"
                class="px-7 py-2.5 rounded-lg border border-slate-300 dark:border-[#FFFFFF] dark:text-[#FFFFFF] text-[13px] font-medium text-slate-600 hover:bg-slate-50 dark:hover:bg-[#2a2a2a] transition-colors cursor-pointer bg-white dark:bg-transparent">
                Batal
            </button>
            <button onclick="confirmDeleteAcUnit()"
                class="px-7 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-[13px] font-semibold transition-colors cursor-pointer">
                Hapus
            </button>
        </div>
    </div>
</div>

<script>
const AC_ROUTES = {
    store:   '{{ route('pengaturan.acunits.store') }}',
    update:  '{{ route('pengaturan.acunits.update', ['acUnit' => '__ID__']) }}',
    destroy: '{{ route('pengaturan.acunits.destroy', ['acUnit' => '__ID__']) }}',
    toggle:  '{{ route('pengaturan.acunits.toggle', ['acUnit' => '__ID__']) }}',
};
const AC_CSRF = '{{ csrf_token() }}';

let _editAcId = null;

function openAcModal(id = null, name = '', roomId = '', power = 0, isActive = 0) {
    _editAcId = id;
    document.getElementById('acModalId').value   = id || '';
    document.getElementById('acModalName').value      = name;
    document.getElementById('acModalRoomId').value    = roomId || '';
    document.getElementById('acModalPower').value     = power || '';
    document.getElementById('acModalActive').checked  = isActive == 1;
    document.getElementById('acModalActiveLabel').textContent = isActive == 1 ? 'ON' : 'OFF';
    document.getElementById('acModalTitle').textContent = id ? 'Edit Perangkat' : 'Tambah Perangkat';
    document.getElementById('acModalSaveBtn').textContent = 'Simpan';

    const modal = document.getElementById('acModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => document.getElementById('acModalName').focus(), 50);
}

function closeAcModal() {
    document.getElementById('acModal').classList.add('hidden');
    document.getElementById('acModal').classList.remove('flex');
    _editAcId = null;
}

function saveAcUnit() {
    const name     = document.getElementById('acModalName').value.trim();
    const roomId   = document.getElementById('acModalRoomId').value;
    const power    = parseFloat(document.getElementById('acModalPower').value) || 0;
    const isActive = document.getElementById('acModalActive').checked ? 1 : 0;

    if (!name)   { showAcToast('Nama perangkat wajib diisi.'); return; }
    if (!roomId) { showAcToast('Pilih ruangan terlebih dahulu.'); return; }

    document.getElementById('acModalSaveBtn').textContent = 'Menyimpan...';

    const isEdit = !!_editAcId;
    const url    = isEdit ? AC_ROUTES.update.replace('__ID__', _editAcId) : AC_ROUTES.store;
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': AC_CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ name, room_id: roomId, power_kw: power, is_active: isActive }),
    })
    .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
    .then(({ ok, data }) => {
        if (!ok) {
            document.getElementById('acModalSaveBtn').textContent = 'Simpan';
            const msgs = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Gagal menyimpan.');
            showAcToast('Error: ' + msgs);
            return;
        }
        closeAcModal();
        showAcToast(isEdit ? 'Perangkat berhasil diperbarui ✓' : 'Perangkat berhasil ditambahkan ✓');
        setTimeout(() => location.reload(), 900);
    })
    .catch(() => {
        document.getElementById('acModalSaveBtn').textContent = 'Simpan';
        showAcToast('Terjadi kesalahan jaringan.');
    });
}

function toggleAcStatus(id, isCurrentlyOn) {
    if (!confirm((isCurrentlyOn ? 'Matikan' : 'Aktifkan') + ' perangkat ini?')) return;
    fetch(AC_ROUTES.toggle.replace('__ID__', id), {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': AC_CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAcToast(isCurrentlyOn ? 'Perangkat dimatikan ✓' : 'Perangkat diaktifkan ✓');
            setTimeout(() => location.reload(), 900);
        }
    })
    .catch(() => showAcToast('Gagal mengubah status perangkat.'));
}

let _deleteAcId = null;

function deleteAcUnit(id, name) {
    _deleteAcId = id;
    document.getElementById('delete-ac-name').textContent = name;
    document.getElementById('modal-delete-ac').classList.remove('hidden');
}

function closeDeleteAcModal() {
    _deleteAcId = null;
    document.getElementById('modal-delete-ac').classList.add('hidden');
}

function confirmDeleteAcUnit() {
    if (!_deleteAcId) return;
    fetch(AC_ROUTES.destroy.replace('__ID__', _deleteAcId), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': AC_CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        closeDeleteAcModal();
        if (data.success) {
            showAcToast('Perangkat dihapus ✓');
            setTimeout(() => location.reload(), 900);
        }
    })
    .catch(() => { closeDeleteAcModal(); showAcToast('Gagal menghapus perangkat.'); });
}

document.getElementById('modal-delete-ac').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteAcModal();
});

// Toggle label ON/OFF
document.getElementById('acModalActive').addEventListener('change', function () {
    document.getElementById('acModalActiveLabel').textContent = this.checked ? 'ON' : 'OFF';
});

document.getElementById('acModal').addEventListener('click', function (e) {
    if (e.target === this) closeAcModal();
});

function showAcToast(msg) {
    const t = document.getElementById('ac-toast');
    t.textContent = msg;
    t.classList.remove('hidden');
    clearTimeout(t._tmr);
    t._tmr = setTimeout(() => t.classList.add('hidden'), 2800);
}

// ── Detail Perangkat Modal ────────────────────────────────────────────────────
function openAcDetail(name, roomId, roomName, power, isActive, roomActive, updatedAt) {
    document.getElementById('acdName').textContent    = name || '—';
    document.getElementById('acdRoom').textContent    = roomName || '—';
    document.getElementById('acdUpdated').textContent = updatedAt || '—';

    // Monitoring badge
    document.getElementById('acdMonitoring').innerHTML = roomActive
        ? '<span class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-green-600"><span class="w-2 h-2 rounded-full bg-green-500"></span>Aktif</span>'
        : '<span class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-slate-400"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Nonaktif</span>';

    // Status badge
    document.getElementById('acdStatus').innerHTML = isActive
        ? '<span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11.5px] font-bold bg-green-500 text-white">ON</span>'
        : '<span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11.5px] font-bold bg-slate-700 text-white">OFF</span>';

    const modal = document.getElementById('acDetailModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAcDetail() {
    const modal = document.getElementById('acDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('acDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeAcDetail();
});
</script>
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
