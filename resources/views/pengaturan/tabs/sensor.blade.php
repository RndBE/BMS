{{-- ═══ TAB SENSOR ═══ --}}

<div class="flex items-center justify-between mb-5">
    <h2 class="text-[16px] font-bold text-slate-800">Daftar Sensor</h2>
    <div class="flex items-center gap-3">
        {{-- Search --}}
        <form method="GET" action="{{ route('pengaturan.konfigurasi') }}" class="flex items-center">
            <input type="hidden" name="tab" value="sensor">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari sensor / ruangan..."
                    class="pl-8 pr-3 py-[7px] border border-slate-200 rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 w-52">
            </div>
        </form>
        {{-- Tambah Sensor --}}
        <button onclick="openSensorModal()" class="flex items-center gap-1.5 bg-red-700 hover:bg-red-800 text-white text-[12.5px] font-semibold px-4 py-[8px] rounded-lg transition-colors cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Sensor
        </button>
    </div>
</div>

{{-- Table --}}
<div class="overflow-x-auto">
    <table class="w-full text-[13px]">
        <thead>
            <tr class="bg-red-50 text-slate-600 text-left">
                <th class="px-4 py-2.5 font-semibold rounded-l-lg">Gambar Sensor</th>
                <th class="px-4 py-2.5 font-semibold">Nama Sensor</th>
                <th class="px-4 py-2.5 font-semibold">Tipe Sensor</th>
                <th class="px-4 py-2.5 font-semibold">Ruangan</th>
                <th class="px-4 py-2.5 font-semibold">Parameter</th>
                <th class="px-4 py-2.5 font-semibold">Status</th>
                <th class="px-4 py-2.5 font-semibold text-center rounded-r-lg">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($sensors as $sensor)
                @php
                    $grupNama = $sensor->sensorGroup?->nama_sensor ?? '-';
                    $grupKode = $sensor->sensorGroup?->kode_sensor ?? '-';

                    // Tipe sensor: dari field tipe_sensor sensor, atau type enum sebagai fallback
                    $tipeSensor = $sensor->tipe_sensor
                                ?: ($sensor->type
                                    ? ucfirst($sensor->type) . ' Sensor'
                                    : null);

                    // Parameter dari room (max 3 ditampilkan, sisanya +N)
                    $params = $sensor->room?->parameters ?? collect();

                    // Warna badge parameter berdasarkan nama
                    $paramColors = [
                        'suhu'           => 'bg-orange-50 text-orange-600 ring-1 ring-orange-200',
                        'temperature'    => 'bg-orange-50 text-orange-600 ring-1 ring-orange-200',
                        'kelembaban'     => 'bg-blue-50 text-blue-600 ring-1 ring-blue-200',
                        'humidity'       => 'bg-blue-50 text-blue-600 ring-1 ring-blue-200',
                        'energi'         => 'bg-yellow-50 text-yellow-600 ring-1 ring-yellow-200',
                        'energy'         => 'bg-yellow-50 text-yellow-600 ring-1 ring-yellow-200',
                        'daya'           => 'bg-purple-50 text-purple-600 ring-1 ring-purple-200',
                        'power'          => 'bg-purple-50 text-purple-600 ring-1 ring-purple-200',
                        'co₂'            => 'bg-green-50 text-green-600 ring-1 ring-green-200',
                        'co2'            => 'bg-green-50 text-green-600 ring-1 ring-green-200',
                    ];
                @endphp
                <tr class="hover:bg-slate-50/60 transition-colors">

                    {{-- Gambar Sensor --}}
                    <td class="px-4 py-3">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                            @if($sensor->gambar)
                                <img src="{{ Storage::url($sensor->gambar) }}"
                                     alt="{{ $grupNama }}"
                                     class="w-full h-full object-cover">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                                     stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="text-slate-400">
                                    <path d="M12 2a3 3 0 0 1 3 3v7a3 3 0 0 1-6 0V5a3 3 0 0 1 3-3z"/>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                                    <line x1="12" y1="19" x2="12" y2="22"/>
                                </svg>
                            @endif
                        </div>
                    </td>

                    {{-- Nama Sensor --}}
                    <td class="px-4 py-3">
                        <p class="font-semibold text-slate-800">{{ $grupNama }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            ID: {{ $grupKode }}
                            @if($sensor->unit) · {{ $sensor->unit }} @endif
                        </p>
                    </td>

                    {{-- Tipe Sensor --}}
                    <td class="px-4 py-3 text-slate-600">
                        {{ $tipeSensor ?? '-' }}
                    </td>

                    {{-- Ruangan --}}
                    <td class="px-4 py-3 text-slate-600">
                        {{ $sensor->room?->name ?? '-' }}
                    </td>

                    {{-- Parameter (badge dari room->parameters) --}}
                    <td class="px-4 py-3">
                        @if($params->isNotEmpty())
                            <div class="flex flex-wrap gap-1">
                                @foreach($params->take(3) as $param)
                                    @php
                                        $namaLower = strtolower($param->nama_parameter);
                                        $badgeClass = $paramColors[$namaLower] ?? 'bg-slate-50 text-slate-500 ring-1 ring-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $badgeClass }}">
                                        {{ $param->nama_parameter }}
                                    </span>
                                @endforeach
                                @if($params->count() > 3)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-500">
                                        +{{ $params->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="text-slate-400 text-[12px]">-</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3">
                        @if($sensor->is_active)
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

                    {{-- Aksi --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <button title="Detail" class="text-slate-400 hover:text-slate-700 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                            </button>
                            <button title="Edit"
                                onclick="openSensorModal({{ $sensor->id }})"
                                class="text-slate-400 hover:text-blue-600 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button title="Hapus"
                                onclick="deleteSensor({{ $sensor->id }}, '{{ addslashes($sensor->sensorGroup?->nama_sensor ?? 'Sensor') }}')"
                                class="text-slate-400 hover:text-red-600 transition-colors cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" stroke="currentColor"
                                 stroke-width="1.5" viewBox="0 0 24 24" class="mb-2 opacity-40">
                                <path d="M12 2a3 3 0 0 1 3 3v7a3 3 0 0 1-6 0V5a3 3 0 0 1 3-3z"/>
                                <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                                <line x1="12" y1="19" x2="12" y2="22"/>
                            </svg>
                            <p class="text-[13px]">Tidak ada data sensor</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($sensors instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-50">
    <span class="text-[12px] text-slate-400">
        Menampilkan {{ $sensors->firstItem() ?? 0 }} – {{ $sensors->lastItem() ?? 0 }} dari {{ $sensors->total() }} data
    </span>
    <div class="flex items-center gap-1">
        @if($sensors->onFirstPage())
            <span class="w-7 h-7 flex items-center justify-center rounded text-slate-300 cursor-not-allowed">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </span>
        @else
            <a href="{{ $sensors->previousPageUrl() }}&tab=sensor&search={{ $search }}"
               class="w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:bg-slate-100 no-underline transition-colors">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
        @endif

        @foreach($sensors->getUrlRange(1, $sensors->lastPage()) as $page => $url)
            <a href="{{ $url }}&tab=sensor&search={{ $search }}"
               class="w-7 h-7 flex items-center justify-center rounded text-[12px] font-medium no-underline transition-colors
                   {{ $page == $sensors->currentPage()
                       ? 'bg-red-700 text-white'
                       : 'text-slate-500 hover:bg-slate-100' }}">
                {{ $page }}
            </a>
        @endforeach

        @if($sensors->hasMorePages())
            <a href="{{ $sensors->nextPageUrl() }}&tab=sensor&search={{ $search }}"
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

{{-- ═══ MODAL TAMBAH / EDIT SENSOR ═══ --}}
@push('modals')
<div id="sensorModal" class="hidden fixed inset-0 bg-black/40 z-[1000] items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-[560px] max-w-[95vw] my-6 mx-auto overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="text-[15px] font-bold text-slate-800" id="sensorModalTitle">Tambah Sensor</h3>
            <button onclick="closeSensorModal()" class="text-slate-400 hover:text-slate-700 transition-colors cursor-pointer bg-transparent border-none p-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 flex flex-col gap-4 max-h-[80vh] overflow-y-auto">
            <input type="hidden" id="sensorModalId">

            {{-- Upload Gambar Sensor --}}
            <div>
                <label class="block text-[11.5px] font-semibold text-slate-500 mb-2">Unggah Gambar Sensor</label>
                <div id="sensorDropZone"
                     class="border-2 border-dashed border-slate-200 rounded-xl p-6 flex flex-col items-center justify-center gap-2 cursor-pointer hover:border-red-300 hover:bg-red-50/30 transition-colors"
                     onclick="document.getElementById('sensorGambarInput').click()">
                    <div id="sensorDropPreview" class="hidden w-20 h-20 rounded-lg overflow-hidden mb-1">
                        <img id="sensorDropPreviewImg" src="" alt="" class="w-full h-full object-cover">
                    </div>
                    <div id="sensorDropIcon" class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="text-red-400">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M3 16l5-5 4 4 3-3 6 6"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                        </svg>
                    </div>
                    <p class="text-[12.5px] font-medium text-slate-600 text-center">Tarik file ke sini atau klik untuk upload</p>
                    <p class="text-[11px] text-slate-400">JPG, JPEG, atau PNG. Maksimal 2 MB.</p>
                    <input type="file" id="sensorGambarInput" accept="image/jpg,image/jpeg,image/png" class="hidden">
                </div>
            </div>

            {{-- Nama Sensor (Sensor Group) & Tipe Sensor --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5">Nama Sensor <span class="text-red-500">*</span></label>
                    <select id="sensorModalGroupId"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] text-slate-700 outline-none focus:border-red-400 box-border transition-colors bg-white">
                        <option value="">-- Pilih Sensor Group --</option>
                        @foreach($sensorGroups as $sg)
                            <option value="{{ $sg->id }}">{{ $sg->nama_sensor }} ({{ $sg->kode_sensor }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5">Tipe Sensor</label>
                    <input type="text" id="sensorModalTipe" placeholder="Contoh: Temperature Sensor"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-red-400 box-border transition-colors">
                </div>
            </div>

            {{-- Ruangan --}}
            <div>
                <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5">Ruangan <span class="text-red-500">*</span></label>
                <select id="sensorModalRoomId"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] text-slate-700 outline-none focus:border-red-400 box-border transition-colors bg-white">
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach($allRooms as $r)
                        <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->code }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Status Aktif --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11.5px] font-semibold text-slate-700">Status Sensor</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Aktifkan agar sensor terdeteksi di sistem</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="sensorModalActive" class="sr-only peer" checked>
                    <div class="w-10 h-5 bg-slate-200 rounded-full peer
                                peer-checked:bg-red-600 transition-colors
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                    <span class="ml-2.5 text-[12px] font-semibold" id="sensorModalActiveLabel">Aktif</span>
                </label>
            </div>

            {{-- Daftar Parameter --}}
            <div>
                <p class="text-[12px] font-bold text-slate-700 mb-2">Daftar Parameter</p>
                <div class="rounded-lg border border-slate-100 overflow-hidden">
                    <table class="w-full text-[12px]">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-left">
                                <th class="px-3 py-2 font-semibold">Nama Parameter</th>
                                <th class="px-3 py-2 font-semibold">Kolom Sensor</th>
                                <th class="px-3 py-2 font-semibold">Satuan</th>
                                <th class="px-3 py-2 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sensorParamTableBody" class="divide-y divide-slate-50">
                            {{-- Baris parameter diisi via JS --}}
                        </tbody>
                    </table>
                </div>
                <button type="button" onclick="addParamRow()"
                    class="mt-2 text-[12px] font-semibold text-red-600 hover:text-red-800 flex items-center gap-1 cursor-pointer bg-transparent border-none p-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Parameter
                </button>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-2 px-6 py-4 border-t border-slate-100 bg-slate-50/60">
            <button onclick="closeSensorModal()"
                class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-[13px] hover:bg-slate-100 transition-colors cursor-pointer bg-white">
                Batal
            </button>
            <button onclick="saveSensor()"
                class="px-5 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg text-[13px] font-semibold transition-colors cursor-pointer border-none">
                <span id="sensorModalSaveBtn">Simpan</span>
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="sensor-toast" class="fixed bottom-6 right-6 bg-slate-800 text-white px-[18px] py-2.5 rounded-xl text-[13px] z-[9999] hidden shadow-xl"></div>

<script>
const SENSOR_ROUTES = {
    show:    '{{ route('pengaturan.sensors.show',   ['sensor' => '__ID__']) }}',
    store:   '{{ route('pengaturan.sensors.store') }}',
    update:  '{{ route('pengaturan.sensors.update', ['sensor' => '__ID__']) }}',
    destroy: '{{ route('pengaturan.sensors.destroy', ['sensor' => '__ID__']) }}',
};
const SENSOR_CSRF = '{{ csrf_token() }}';

// Pilihan kolom sensor1–sensor16
const SENSOR_COLS = [
    @for($c = 1; $c <= 16; $c++)
        'sensor{{ $c }}'{{ $c < 16 ? ',' : '' }}
    @endfor
];

let _editSensorId = null;

function openSensorModal(id = null) {
    _editSensorId = id;
    document.getElementById('sensorModalId').value = id || '';
    document.getElementById('sensorParamTableBody').innerHTML = '';
    document.getElementById('sensorDropPreview').classList.add('hidden');
    document.getElementById('sensorDropIcon').classList.remove('hidden');
    document.getElementById('sensorGambarInput').value = '';

    if (!id) {
        // ── MODE TAMBAH ──
        document.getElementById('sensorModalTitle').textContent = 'Tambah Sensor';
        document.getElementById('sensorModalSaveBtn').textContent = 'Simpan';
        document.getElementById('sensorModalGroupId').value = '';
        document.getElementById('sensorModalTipe').value = '';
        document.getElementById('sensorModalRoomId').value = '';
        document.getElementById('sensorModalActive').checked = true;
        document.getElementById('sensorModalActiveLabel').textContent = 'Aktif';
        addParamRow();
        _showSensorModal();
    } else {
        // ── MODE EDIT: fetch data dulu ──
        document.getElementById('sensorModalTitle').textContent = 'Edit Sensor';
        document.getElementById('sensorModalSaveBtn').textContent = 'Memuat...';
        _showSensorModal();

        fetch(SENSOR_ROUTES.show.replace('__ID__', id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': SENSOR_CSRF }
        })
        .then(r => r.json())
        .then(data => {
            // Prefill semua field
            document.getElementById('sensorModalGroupId').value = data.sensor_group_id || '';
            document.getElementById('sensorModalTipe').value    = data.tipe_sensor || '';
            document.getElementById('sensorModalRoomId').value  = data.room_id || '';
            document.getElementById('sensorModalActive').checked = !!data.is_active;
            document.getElementById('sensorModalActiveLabel').textContent = data.is_active ? 'Aktif' : 'Nonaktif';
            document.getElementById('sensorModalSaveBtn').textContent = 'Simpan';

            // Gambar preview
            if (data.gambar_url) {
                document.getElementById('sensorDropPreviewImg').src = data.gambar_url;
                document.getElementById('sensorDropPreview').classList.remove('hidden');
                document.getElementById('sensorDropIcon').classList.add('hidden');
            }

            // Prefill rows parameter
            document.getElementById('sensorParamTableBody').innerHTML = '';
            if (data.parameters && data.parameters.length > 0) {
                data.parameters.forEach(p => addParamRow(p.nama_parameter, p.kolom_reading, p.unit || ''));
            } else {
                addParamRow();
            }
        })
        .catch(() => {
            showSensorToast('Gagal memuat data sensor.');
            closeSensorModal();
        });
    }
}

function _showSensorModal() {
    const modal = document.getElementById('sensorModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeSensorModal() {
    const modal = document.getElementById('sensorModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    _editSensorId = null;
}

function addParamRow(nama = '', kolom = '', satuan = '') {
    const tbody = document.getElementById('sensorParamTableBody');
    const tr = document.createElement('tr');
    tr.className = 'bg-white';

    // Build select options
    const colOptions = SENSOR_COLS.map(c =>
        `<option value="${c}" ${c === kolom ? 'selected' : ''}>${c}</option>`
    ).join('');

    tr.innerHTML = `
        <td class="px-3 py-1.5">
            <input type="text" placeholder="Nama Parameter" value="${nama}"
                class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-[12px] outline-none focus:border-red-400 param-nama">
        </td>
        <td class="px-3 py-1.5">
            <div class="relative">
                <select class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-[12px] outline-none focus:border-red-400 appearance-none bg-white cursor-pointer param-kolom">
                    ${colOptions}
                </select>
                <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </td>
        <td class="px-3 py-1.5">
            <input type="text" placeholder="°C, %, ppm..." value="${satuan}"
                class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-[12px] outline-none focus:border-red-400 param-satuan">
        </td>
        <td class="px-3 py-1.5 text-center">
            <button onclick="this.closest('tr').remove()"
                class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-500 mx-auto cursor-pointer border-none transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

function saveSensor() {
    const roomId   = document.getElementById('sensorModalRoomId').value;
    const groupId  = document.getElementById('sensorModalGroupId').value;
    const tipe     = document.getElementById('sensorModalTipe').value.trim();
    const isActive = document.getElementById('sensorModalActive').checked;
    const file     = document.getElementById('sensorGambarInput').files[0];

    if (!roomId) { showSensorToast('Pilih ruangan terlebih dahulu.'); return; }

    // Kumpulkan parameter dari rows
    const rows = document.querySelectorAll('#sensorParamTableBody tr');
    const parameters = [];
    for (const row of rows) {
        const nama  = row.querySelector('.param-nama')?.value.trim();
        const kolom = row.querySelector('.param-kolom')?.value;
        const sat   = row.querySelector('.param-satuan')?.value.trim();
        if (nama && kolom) parameters.push({ nama_parameter: nama, kolom_reading: kolom, unit: sat });
    }

    document.getElementById('sensorModalSaveBtn').textContent = 'Menyimpan...';

    const isEdit = !!_editSensorId;
    const url    = isEdit ? SENSOR_ROUTES.update.replace('__ID__', _editSensorId) : SENSOR_ROUTES.store;

    const fd = new FormData();
    if (isEdit) fd.append('_method', 'PUT');
    fd.append('_token', SENSOR_CSRF);
    fd.append('room_id', roomId);
    if (groupId)  fd.append('sensor_group_id', groupId);
    if (tipe)     fd.append('tipe_sensor', tipe);
    fd.append('is_active', isActive ? '1' : '0');
    if (file)     fd.append('gambar', file);
    parameters.forEach((p, i) => {
        fd.append(`parameters[${i}][nama_parameter]`, p.nama_parameter);
        fd.append(`parameters[${i}][kolom_reading]`,  p.kolom_reading);
        fd.append(`parameters[${i}][unit]`,           p.unit || '');
    });

    fetch(url, { method: 'POST', headers: { 'Accept': 'application/json' }, body: fd })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (!ok) {
                document.getElementById('sensorModalSaveBtn').textContent = 'Simpan';
                const msgs = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Gagal menyimpan.');
                showSensorToast('Error: ' + msgs);
                return;
            }
            closeSensorModal();
            showSensorToast(isEdit ? 'Sensor berhasil diperbarui ✓' : 'Sensor berhasil ditambahkan ✓');
            setTimeout(() => location.reload(), 900);
        })
        .catch(() => {
            document.getElementById('sensorModalSaveBtn').textContent = 'Simpan';
            showSensorToast('Terjadi kesalahan jaringan.');
        });
}

function deleteSensor(id, nama) {
    if (!confirm('Hapus sensor "' + nama + '"? Tindakan ini tidak dapat dibatalkan.')) return;
    fetch(SENSOR_ROUTES.destroy.replace('__ID__', id), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': SENSOR_CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSensorToast('Sensor dihapus ✓');
            setTimeout(() => location.reload(), 900);
        }
    })
    .catch(() => showSensorToast('Gagal menghapus sensor.'));
}

// Preview gambar saat file dipilih
document.getElementById('sensorGambarInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('sensorDropPreviewImg').src = e.target.result;
        document.getElementById('sensorDropPreview').classList.remove('hidden');
        document.getElementById('sensorDropIcon').classList.add('hidden');
    };
    reader.readAsDataURL(file);
});

// Drag and drop
const dropZone = document.getElementById('sensorDropZone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-red-400', 'bg-red-50/40'); });
dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('border-red-400', 'bg-red-50/40'); });
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('border-red-400', 'bg-red-50/40');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('sensorGambarInput').files = dt.files;
        document.getElementById('sensorGambarInput').dispatchEvent(new Event('change'));
    }
});

// Toggle active label
document.getElementById('sensorModalActive').addEventListener('change', function () {
    document.getElementById('sensorModalActiveLabel').textContent = this.checked ? 'Aktif' : 'Nonaktif';
});

// Close on backdrop click
document.getElementById('sensorModal').addEventListener('click', function (e) {
    if (e.target === this) closeSensorModal();
});

function showSensorToast(msg) {
    const t = document.getElementById('sensor-toast');
    t.textContent = msg;
    t.classList.remove('hidden');
    clearTimeout(t._tmr);
    t._tmr = setTimeout(() => t.classList.add('hidden'), 2800);
}
</script>
@endpush
