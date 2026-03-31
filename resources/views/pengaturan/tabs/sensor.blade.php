{{-- ═══ TAB SENSOR ═══ --}}

<div class="flex items-center justify-between mb-5">
    <h2 class="text-[16px] font-bold text-slate-800 dark:text-white">Daftar Sensor</h2>
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
                    class="pl-8 pr-3 py-[7px] border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 w-52">
            </div>
        </form>
        {{-- Tambah Sensor --}}
        <button type="button" onclick="openSensorModal()" class="flex items-center gap-1.5 bg-red-700 hover:bg-red-800 text-white text-[12.5px] font-semibold px-4 py-[7px] rounded-lg transition-colors cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Sensor
        </button>
    </div>
</div>

{{-- Table --}}
<div class="overflow-x-auto">
    <table class="w-full text-[13px]">
        <thead>
            <tr class="bg-red-100 text-slate-800 text-left dark:bg-[#1D1D1D] dark:text-white">
                <th class="px-4 py-2.5 font-semibold rounded-l-lg text-center">Gambar Sensor</th>
                <th class="px-4 py-2.5 font-semibold">Nama Sensor</th>
                <th class="px-4 py-2.5 font-semibold">Tipe Sensor</th>
                <th class="px-4 py-2.5 font-semibold">Ruangan</th>
                <th class="px-4 py-2.5 font-semibold">Parameter</th>
                <th class="px-4 py-2.5 font-semibold">Status</th>
                <th class="px-4 py-2.5 font-semibold text-center rounded-r-lg">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 dark:divide-[#1D1D1D]">
            @forelse($sensors as $sensor)
                @php
                    $grupNama = $sensor->sensorGroup?->nama_sensor ?? '-';
                    $grupKode = $sensor->sensorGroup?->kode_sensor ?? '-';

                    // Tipe sensor: dari field tipe_sensor saja
                    $tipeSensor = $sensor->tipe_sensor ?: null;

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
                <tr class="hover:bg-slate-50/60 dark:hover:bg-transparent transition-colors">
                    {{-- Gambar Sensor --}}
                    <td class="px-4 py-3 text-center">
                        <div class="w-16 h-16 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden mx-auto">
                            @if($sensor->gambar)
                                <img src="{{ Storage::url($sensor->gambar) }}"
                                     alt="{{ $grupNama }}"
                                     class="w-full h-full object-cover">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                     stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="text-slate-300">
                                    <path d="M12 2a3 3 0 0 1 3 3v7a3 3 0 0 1-6 0V5a3 3 0 0 1 3-3z"/>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                                    <line x1="12" y1="19" x2="12" y2="22"/>
                                </svg>
                            @endif
                        </div>
                    </td>

                    {{-- Nama Sensor --}}
                    <td class="px-4 py-3">
                        <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $grupNama }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5 dark:text-slate-200">ID: {{ $grupKode }}</p>
                    </td>

                    {{-- Tipe Sensor --}}
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-200">
                        {{ $tipeSensor ?? '-' }}
                    </td>

                    {{-- Ruangan --}}
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-200">
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
                            <button title="Detail" onclick="openSensorDetail({{ $sensor->id }})" class="text-slate-400 hover:text-slate-700 transition-colors cursor-pointer">
                                <img src="{{ asset('icons/detail.svg') }}" alt="Detail" class="w-7 h-7">
                            </button>
                            <button title="Edit"
                                onclick="openSensorModal({{ $sensor->id }})"
                                class="text-slate-400 hover:text-blue-600 transition-colors cursor-pointer">
                                <img src="{{ asset('icons/edit.svg') }}" alt="Edit" class="w-7 h-7">
                            </button>
                            <button title="Hapus"
                                onclick="deleteSensor({{ $sensor->id }}, '{{ addslashes($sensor->sensorGroup?->nama_sensor ?? 'Sensor') }}')"
                                class="text-slate-400 hover:text-red-600 transition-colors cursor-pointer">
                                <img src="{{ asset('icons/hapus.svg') }}" alt="Hapus" class="w-7 h-7">
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
<div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-50 dark:border-[#1D1D1D]">
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
                       ? 'bg-red-700 text-white dark:border-[#FDEBEB] dark:bg-[#FDEBEB] dark:text-black'
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

{{-- ── Detail Modal ─────────────────────────────────────────────────────── --}}
<div id="sensorDetailModal" class="hidden fixed inset-0 bg-black/40 z-[1001] items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-[520px] max-w-[95vw] mx-auto overflow-hidden relative dark:border-[#3d3d3d] dark:bg-[#2a2a2a]">

        {{-- Close --}}
        <button onclick="closeSensorDetail()"
            class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors cursor-pointer border-none bg-transparent z-10 dark:hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        {{-- Title --}}
        <div class="px-7 pt-6 pb-1">
            <h3 class="text-[15px] font-bold text-slate-800 dark:text-white">Detail Sensor</h3>
        </div>

        {{-- Body --}}
        <div class="flex gap-5 px-7 py-5 pb-7">

            {{-- Gambar (diisi JS via openSensorDetail) --}}
            <div class="shrink-0 w-[140px] h-[140px] rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center">
                <img id="sdImg" src="" alt="" class="w-full h-full object-cover hidden">
                <svg id="sdImgFallback" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none"
                     stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" class="text-slate-300">
                    <path d="M12 2a3 3 0 0 1 3 3v7a3 3 0 0 1-6 0V5a3 3 0 0 1 3-3z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                    <line x1="12" y1="19" x2="12" y2="22"/>
                </svg>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0 flex flex-col gap-4 justify-center ">

                {{-- Nama --}}
                <div>
                    <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Nama Sensor</p>
                    <p id="sdName" class="text-[15px] font-bold text-slate-800 leading-tight ">—</p>
                    <p id="sdCode" class="text-[12px] text-slate-400 mt-0.5">—</p>
                </div>

                {{-- Tipe + Ruangan --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Tipe Sensor</p>
                        <p id="sdTipe" class="text-[13px] font-semibold text-slate-700">—</p>
                    </div>
                    <div>
                        <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Ruangan</p>
                        <p id="sdRoom" class="text-[13px] font-semibold text-slate-700">—</p>
                    </div>
                </div>

                {{-- Parameter --}}
                <div>
                    <p class="text-[9.5px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Parameter</p>
                    <div id="sdParams" class="flex flex-wrap gap-1.5">
                        <span class="text-[12px] text-slate-400">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="sensorModal" class="hidden fixed inset-0 bg-black/40 z-[1000] items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-[560px] max-w-[95vw] my-6 mx-auto overflow-hidden dark:border-[#232323] dark:bg-[#2a2a2a]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-[#232323]">
            <h3 class="text-[15px] font-bold text-slate-800 dark:text-white" id="sensorModalTitle">Tambah Sensor</h3>
            <button onclick="closeSensorModal()" class="text-slate-400 hover:text-slate-700 transition-colors cursor-pointer bg-transparent border-none p-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 flex flex-col gap-4 max-h-[80vh] overflow-y-auto">
            <input type="hidden" id="sensorModalId">

            <div>
                <label class="block text-[11.5px] font-semibold text-slate-500 mb-2 dark:text-slate-200">Unggah Gambar Sensor</label>

                {{-- Drop Zone --}}
                <div id="sensorDropZone"
                     class="border-2 border-dashed border-slate-200 rounded-xl p-5 flex flex-col items-center justify-center gap-2 cursor-pointer hover:border-red-300 hover:bg-red-50/30 transition-colors dark:border-[#3d3d3d] dark:bg-[#2a2a2a]"
                     onclick="document.getElementById('sensorGambarInput').click()">
                    <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg width="26" height="26" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-red-400">
                            <path d="M26.5625 16.25V10.625C26.5625 8.9674 25.904 7.37769 24.7319 6.20558C23.5598 5.03348 21.9701 4.375 20.3125 4.375H9.6875C8.0299 4.375 6.44019 5.03348 5.26808 6.20558C4.09598 7.37769 3.4375 8.9674 3.4375 10.625V19.375C3.4375 20.1958 3.59916 21.0085 3.91325 21.7668C4.22734 22.5251 4.68772 23.2141 5.26808 23.7944C6.44019 24.9665 8.0299 25.625 9.6875 25.625H17.5125" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3.7627 21.25L7.1877 17.25C7.6375 16.8033 8.22783 16.5257 8.85879 16.4642C9.48976 16.4027 10.1226 16.5611 10.6502 16.9125C11.1778 17.264 11.8106 17.4224 12.4416 17.3609C13.0726 17.2994 13.6629 17.0218 14.1127 16.575L17.0252 13.6625C17.8621 12.8228 18.9701 12.3078 20.1515 12.2093C21.3329 12.1109 22.5109 12.4354 23.4752 13.125L26.5627 15.5125M10.0127 12.7125C10.2852 12.7109 10.5547 12.6556 10.8058 12.5498C11.0569 12.444 11.2848 12.2898 11.4763 12.0959C11.6678 11.9021 11.8193 11.6724 11.922 11.4201C12.0248 11.1677 12.0768 10.8975 12.0752 10.625C12.0736 10.3525 12.0183 10.083 11.9125 9.83192C11.8067 9.5808 11.6524 9.35297 11.4586 9.16145C11.2648 8.96993 11.0351 8.81847 10.7827 8.7157C10.5303 8.61294 10.2602 8.5609 9.98769 8.56254C9.43737 8.56585 8.9109 8.78765 8.52411 9.17913C8.13732 9.57061 7.92188 10.0997 7.9252 10.65C7.92851 11.2004 8.15031 11.7268 8.54179 12.1136C8.93327 12.5004 9.46237 12.7159 10.0127 12.7125Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M23.3838 18.75V25" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
                            <path d="M26.2501 21.3813L23.7913 18.9225C23.7379 18.8688 23.6744 18.8262 23.6045 18.7972C23.5345 18.7681 23.4596 18.7532 23.3838 18.7532C23.3081 18.7532 23.2331 18.7681 23.1632 18.7972C23.0933 18.8262 23.0298 18.8688 22.9763 18.9225L20.5176 21.3813" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <p class="text-[12.5px] font-medium text-slate-600 dark:text-slate-200">Tarik file ke sini atau klik untuk upload</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-200">JPG, JPEG, atau PNG · Maks. 2 MB</p>
                    <input type="file" id="sensorGambarInput" accept="image/jpg,image/jpeg,image/png" class="hidden">
                </div>

                {{-- File Item (muncul setelah file dipilih) --}}
                <div id="sensorFileItem" class="hidden mt-3 border border-slate-200 rounded-xl overflow-hidden">
                    <div class="flex items-center gap-3 px-4 py-3">
                        {{-- Thumbnail --}}
                        <div class="w-9 h-9 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                            <img id="sensorFileThumbnail" src="" alt="" class="w-full h-full object-cover">
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p id="sensorFileName" class="text-[12.5px] font-semibold text-slate-800 truncate dark:text-slate-200">-</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <p id="sensorFileSize" class="text-[11px] text-slate-400">0 KB</p>
                                <span class="text-slate-300 text-[10px]">•</span>
                                <p id="sensorFileStatus" class="text-[11px] text-slate-400 flex items-center gap-1">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                    Siap diupload
                                </p>
                            </div>
                            {{-- Progress bar --}}
                            <div class="mt-1.5 h-1 bg-slate-100 rounded-full overflow-hidden">
                                <div id="sensorFileProgress"
                                     class="h-full bg-red-600 rounded-full transition-all duration-300"
                                     style="width:0%">
                                </div>
                            </div>
                        </div>
                        {{-- Remove --}}
                        <button type="button" onclick="removeSensorFile()"
                            class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Nama Sensor (Sensor Group) & Tipe Sensor --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5 dark:text-slate-200">Nama Sensor <span class="text-red-500">*</span></label>
                    <div class="relative custom-select-wrapper">
                        <select id="sensorModalGroupId" class="hidden real-select">
                            <option value="">-- Pilih Sensor Group --</option>
                            @foreach($sensorGroups as $sg)
                                <option value="{{ $sg->id }}">{{ $sg->nama_sensor }} ({{ $sg->kode_sensor }})</option>
                            @endforeach
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-2 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer">
                            <span class="select-text truncate text-left">-- Pilih Sensor Group --</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-[60] text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                </div>
                <div>
                    <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5 dark:text-slate-200">Tipe Sensor</label>
                    <input type="text" id="sensorModalTipe" placeholder="Contoh: Temperature Sensor"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-red-400 box-border transition-colors dark:border-[#3C3D3F] dark:bg-[#3C3D3F] dark:text-slate-200">
                </div>
            </div>

            {{-- Ruangan --}}
            <div>
                <label class="block text-[11.5px] font-semibold text-slate-500 mb-1.5 dark:text-slate-200">Ruangan <span class="text-red-500">*</span></label>
                <div class="relative custom-select-wrapper">
                    <select id="sensorModalRoomId" class="hidden real-select">
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach($allRooms as $r)
                            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->code }})</option>
                        @endforeach
                    </select>
                    <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-2 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer">
                        <span class="select-text truncate text-left">-- Pilih Ruangan --</span>
                        <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-[60] text-[13px] text-slate-700 dark:text-slate-200"></ul>
                </div>
            </div>

            {{-- Status Aktif --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11.5px] font-semibold text-slate-700 dark:text-slate-200">Status Sensor</p>
                    <p class="text-[11px] text-slate-400 mt-0.5 dark:text-slate-200">Aktifkan agar sensor terdeteksi di sistem</p>
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
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-50 dark:bg-[#1D1D1D] dark:border-[#1D1D1D]">
                <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3 dark:bg-[#1D1D1D] dark:border-[#1D1D1D]">
                    <p class="text-[12px] font-bold text-slate-700 dark:text-slate-200">Daftar Parameter</p>
                </div>
                <div class="border border-slate-100 overflow-hidden dark:bg-[#232323] dark:border-[#232323]">
                    <table class="w-full text-[12px]" style="table-layout:fixed">
                        <thead>
                            <tr class="bg-white text-slate-700 text-left dark:bg-[#232323] dark:border-[#232323] dark:text-slate-200"
                                style="display:table;width:100%;table-layout:fixed">
                                <th class="px-3 py-2 font-semibold">Nama Parameter</th>
                                <th class="px-3 py-2 font-semibold">Kolom Sensor</th>
                                <th class="px-3 py-2 font-semibold">Satuan</th>
                                <th class="px-3 py-2 font-semibold text-center w-12">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sensorParamTableBody"
                               class="divide-y divide-slate-50 dark:divide-[#1D1D1D]"
                               style="display:block;max-height:160px;overflow-y:auto">
                            {{-- Baris parameter diisi via JS --}}
                        </tbody>
                        <tfoot>
                            <tr class="bg-white border-t border-slate-100 dark:bg-[#232323] dark:border-[#232323] dark:text-slate-200"
                                style="display:table;width:100%;table-layout:fixed">
                                <td colspan="4" class="px-3 py-2">
                                    <button type="button" onclick="addParamRow()"
                                        class="text-[12px] font-semibold text-red-600 hover:text-red-800 flex items-center gap-1 cursor-pointer bg-transparent border-none p-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Tambah Parameter
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-2 px-6 py-4 border-t border-slate-100 bg-slate-50/60 dark:border-[#232323] dark:bg-[#2a2a2a]">
            <button onclick="closeSensorModal()"
                class="px-4 py-2 border border-slate-200 text-slate-600 dark:border-[#FFFFFF] dark:text-[#FFFFFF] dark:bg-transparent rounded-lg text-[13px] hover:bg-slate-100 transition-colors cursor-pointer bg-white">
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

{{-- Modal Konfirmasi Hapus Sensor --}}
<div id="modal-delete-sensor" class="hidden fixed inset-0 bg-black/50 z-[1100] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#232323] rounded-2xl shadow-2xl w-full max-w-sm text-center overflow-hidden">
        <div class="px-8 pt-8 pb-6">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('icons/delete.svg') }}" alt="Hapus" class="w-12 h-12">
            </div>
            <h3 class="text-[16px] font-bold text-slate-800 dark:text-white mb-2">Hapus Sensor</h3>
            <p class="text-[13px] text-slate-500 dark:text-slate-400">
                Anda yakin ingin menghapus sensor <strong id="delete-sensor-name" class="text-slate-700 dark:text-slate-200"></strong>?
                <br>Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div class="flex justify-center gap-3 px-8 pb-7">
            <button onclick="closeDeleteSensorModal()"
                class="px-7 py-2.5 rounded-lg border border-slate-300 dark:border-[#FFFFFF] dark:text-[#FFFFFF] text-[13px] font-medium text-slate-600 hover:bg-slate-50 dark:hover:bg-[#2a2a2a] transition-colors cursor-pointer bg-white dark:bg-transparent">
                Batal
            </button>
            <button onclick="confirmDeleteSensor()"
                class="px-7 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-[13px] font-semibold transition-colors cursor-pointer">
                Hapus
            </button>
        </div>
    </div>
</div>

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
    document.getElementById('sensorGambarInput').value = '';
    // Reset upload UI
    document.getElementById('sensorFileItem').classList.add('hidden');
    document.getElementById('sensorDropZone').classList.remove('hidden');
    document.getElementById('sensorFileProgress').style.width = '0%';

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

            // Gambar existing: tampilkan di file-item sebagai preview
            if (data.gambar_url) {
                const fname = data.gambar_url.split('/').pop();
                document.getElementById('sensorFileThumbnail').src  = data.gambar_url;
                document.getElementById('sensorFileName').textContent = fname;
                document.getElementById('sensorFileSize').textContent  = 'Gambar tersimpan';
                document.getElementById('sensorFileStatus').innerHTML  =
                    '<span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500"></span> Terpasang';
                document.getElementById('sensorFileProgress').style.width = '100%';
                document.getElementById('sensorFileItem').classList.remove('hidden');
                document.getElementById('sensorDropZone').classList.add('hidden');
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
    tr.className = 'bg-white dark:bg-[#232323] dark:border-[#232323] dark:text-slate-200';
    tr.style.cssText = 'display:table;width:100%;table-layout:fixed';

    // Build select options
    const colOptions = SENSOR_COLS.map(c =>
        `<option value="${c}" ${c === kolom ? 'selected' : ''}>${c}</option>`
    ).join('');

    tr.innerHTML = `
        <td class="px-3 py-1.5">
            <input type="text" placeholder="Nama Parameter" value="${nama}"
                class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-[12px] outline-none focus:border-red-400 param-nama dark:bg-[#3C3D3F] dark:border-[#3C3D3F] dark:text-slate-200">
        </td>
        <td class="px-3 py-1.5">
            <div class="relative">
                <select class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-[12px] outline-none focus:border-red-400 appearance-none bg-white cursor-pointer param-kolom dark:bg-[#3C3D3F] dark:border-[#3C3D3F] dark:text-slate-200">
                    ${colOptions}
                </select>
            </div>
        </td>
        <td class="px-3 py-1.5">
            <input type="text" placeholder="°C, %, ppm..." value="${satuan}"
                class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-[12px] outline-none focus:border-red-400 param-satuan dark:bg-[#3C3D3F] dark:border-[#3C3D3F] dark:text-slate-200">
        </td>
        <td class="px-3 py-1.5 text-center">
            <button onclick="this.closest('tr').remove()"
                class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-500 mx-auto cursor-pointer border-none transition-colors">
                <img src="{{ asset('icons/trash.svg') }}" alt="Hapus" class="w-7 h-7">
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

let _deleteSensorId = null;

function deleteSensor(id, nama) {
    _deleteSensorId = id;
    document.getElementById('delete-sensor-name').textContent = nama;
    document.getElementById('modal-delete-sensor').classList.remove('hidden');
}

function closeDeleteSensorModal() {
    _deleteSensorId = null;
    document.getElementById('modal-delete-sensor').classList.add('hidden');
}

function confirmDeleteSensor() {
    if (!_deleteSensorId) return;
    fetch(SENSOR_ROUTES.destroy.replace('__ID__', _deleteSensorId), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': SENSOR_CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        closeDeleteSensorModal();
        if (data.success) {
            showSensorToast('Sensor dihapus ✓');
            setTimeout(() => location.reload(), 900);
        }
    })
    .catch(() => { closeDeleteSensorModal(); showSensorToast('Gagal menghapus sensor.'); });
}

document.getElementById('modal-delete-sensor').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteSensorModal();
});


// Helpers: format bytes
function fmtBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(0) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function removeSensorFile() {
    document.getElementById('sensorGambarInput').value = '';
    document.getElementById('sensorFileItem').classList.add('hidden');
    document.getElementById('sensorDropZone').classList.remove('hidden');
    // Reset progress
    document.getElementById('sensorFileProgress').style.width = '0%';
}

// File selected: show file-item with animated progress
document.getElementById('sensorGambarInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const totalKB = Math.round(file.size / 1024);
    document.getElementById('sensorFileName').textContent  = file.name;
    document.getElementById('sensorFileSize').textContent  = '0 KB of ' + fmtBytes(file.size);
    document.getElementById('sensorFileStatus').innerHTML  =
        '<span class="inline-block w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Menggunggah...';
    document.getElementById('sensorFileProgress').style.width = '0%';
    document.getElementById('sensorFileItem').classList.remove('hidden');
    document.getElementById('sensorDropZone').classList.add('hidden');

    // Animate progress while FileReader loads
    const bar = document.getElementById('sensorFileProgress');
    const sizeEl = document.getElementById('sensorFileSize');
    const statusEl = document.getElementById('sensorFileStatus');
    let prog = 0;
    const step = Math.max(1, Math.round(totalKB / 20));
    const iv = setInterval(() => {
        prog = Math.min(prog + step + Math.random() * step, totalKB * 0.85);
        const pct = Math.min((prog / totalKB) * 100, 85);
        bar.style.width = pct + '%';
        sizeEl.textContent = Math.round(prog) + ' KB of ' + fmtBytes(file.size);
    }, 80);

    const reader = new FileReader();
    reader.onload = (e) => {
        clearInterval(iv);
        bar.style.width = '100%';
        sizeEl.textContent = fmtBytes(file.size) + ' of ' + fmtBytes(file.size);
        statusEl.innerHTML = '<span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500"></span> Selesai';
        // Set thumbnail
        document.getElementById('sensorFileThumbnail').src = e.target.result;
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

// ── Detail Sensor Modal ───────────────────────────────────────────────────────
const PARAM_BADGE_COLORS = {
    'suhu':       'bg-orange-50 text-orange-600 ring-1 ring-orange-200',
    'temperature':'bg-orange-50 text-orange-600 ring-1 ring-orange-200',
    'kelembaban': 'bg-blue-50 text-blue-600 ring-1 ring-blue-200',
    'humidity':   'bg-blue-50 text-blue-600 ring-1 ring-blue-200',
    'energi':     'bg-yellow-50 text-yellow-600 ring-1 ring-yellow-200',
    'daya':       'bg-purple-50 text-purple-600 ring-1 ring-purple-200',
    'co₂':        'bg-green-50 text-green-600 ring-1 ring-green-200',
    'co2':        'bg-green-50 text-green-600 ring-1 ring-green-200',
    'tegangan':   'bg-red-50 text-red-600 ring-1 ring-red-200',
    'arus':       'bg-sky-50 text-sky-600 ring-1 ring-sky-200',
};

function openSensorDetail(id) {
    const modal = document.getElementById('sensorDetailModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Reset state
    document.getElementById('sdImg').classList.add('hidden');
    document.getElementById('sdImgFallback').classList.remove('hidden');
    document.getElementById('sdName').textContent  = 'Memuat...';
    document.getElementById('sdCode').textContent  = '—';
    document.getElementById('sdTipe').textContent  = '—';
    document.getElementById('sdRoom').textContent  = '—';
    document.getElementById('sdParams').innerHTML  = '<span class="text-[12px] text-slate-400">Memuat...</span>';

    fetch(SENSOR_ROUTES.show.replace('__ID__', id), {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': SENSOR_CSRF }
    })
    .then(r => r.json())
    .then(data => {
        // Image
        if (data.gambar_url) {
            document.getElementById('sdImg').src = data.gambar_url;
            document.getElementById('sdImg').classList.remove('hidden');
            document.getElementById('sdImgFallback').classList.add('hidden');
        } else {
            document.getElementById('sdImg').classList.add('hidden');
            document.getElementById('sdImgFallback').classList.remove('hidden');
        }

        // Langsung dari API — tidak perlu scraping select
        document.getElementById('sdName').textContent = data.sensor_group_name || '—';
        document.getElementById('sdCode').textContent = data.sensor_group_code ? 'ID: ' + data.sensor_group_code : '';
        document.getElementById('sdTipe').textContent = data.tipe_sensor || '—';
        document.getElementById('sdRoom').textContent = data.room_name || '—';

        // Parameters
        const params = data.parameters || [];
        if (params.length === 0) {
            document.getElementById('sdParams').innerHTML = '<span class="text-[12px] text-slate-400">Tidak ada parameter</span>';
        } else {
            document.getElementById('sdParams').innerHTML = params.map(p => {
                const key = p.nama_parameter.toLowerCase();
                const cls = PARAM_BADGE_COLORS[key] || 'bg-slate-100 text-slate-600 ring-1 ring-slate-200';
                return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[12px] font-semibold ${cls}">${p.nama_parameter}</span>`;
            }).join('');
        }
    })
    .catch(() => {
        document.getElementById('sdName').textContent = 'Gagal memuat data';
    });
}

function closeSensorDetail() {
    const modal = document.getElementById('sensorDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close detail modal on backdrop click
document.getElementById('sensorDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeSensorDetail();
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
