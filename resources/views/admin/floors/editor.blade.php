@extends('layouts.app')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-3 mb-4">
    <a href="{{ route('admin.buildings.index') }}" class="text-slate-400 no-underline text-[13px] flex items-center gap-1 hover:text-slate-600 transition-colors">
        <i data-feather="arrow-left" class="w-4 h-4"></i> Gedung
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-[13px] text-slate-500">{{ $floor->building->name }}</span>
    <span class="text-slate-300">/</span>
    <span class="text-[14px] font-bold text-slate-800">{{ $floor->name }}</span>
    <span class="ml-auto text-[12px] text-slate-400">Editor Denah</span>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-3.5 py-2 rounded-lg text-[13px] mb-3">✓ {{ session('success') }}</div>
@endif

{{-- Editor Layout --}}
<div class="grid grid-cols-[280px_1fr] gap-4 h-[calc(100vh-130px)]">

    {{-- ═══ LEFT PANEL ═══ --}}
    <div class="bg-white rounded-xl p-4 shadow-[0_1px_4px_rgba(0,0,0,.07)] flex flex-col gap-3 overflow-y-auto">

        {{-- Floor Info --}}
        <div>
            <div class="text-[13px] font-bold text-slate-800 border-b border-slate-100 pb-2">📐 Info Lantai</div>
            <div class="mt-2.5 flex flex-col gap-1.5">
                <div class="flex justify-between items-center text-[12px]">
                    <span class="text-slate-500">Gedung</span>
                    <span class="font-semibold text-slate-800">{{ $floor->building->name }}</span>
                </div>
                <div class="flex justify-between items-center text-[12px]">
                    <span class="text-slate-500">Lantai</span>
                    <span class="font-semibold text-slate-800">{{ $floor->name }}</span>
                </div>
                <div class="flex justify-between items-center text-[12px]">
                    <span class="text-slate-500">Jumlah Ruangan</span>
                    <span class="font-semibold text-slate-800" id="roomCount">{{ $floor->rooms->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Upload Denah --}}
        <div>
            <div class="text-[13px] font-bold text-slate-800 border-b border-slate-100 pb-2">🗺️ Upload Denah</div>
            <form method="POST" action="{{ route('admin.floors.upload', $floor) }}" enctype="multipart/form-data" id="uploadForm" class="mt-2">
                @csrf
                <div class="border-2 border-dashed border-slate-300 hover:border-blue-400 hover:bg-blue-50 rounded-xl p-5 text-center cursor-pointer transition-all"
                    onclick="document.getElementById('planFile').click()">
                    <i data-feather="upload-cloud" class="w-7 h-7 text-slate-400 block mx-auto mb-2"></i>
                    <p class="text-[12px] text-slate-400 m-0">Klik untuk upload<br><strong>PNG, JPG, SVG, PDF</strong><br>Max 20MB</p>
                </div>
                <input type="file" id="planFile" name="plan_file" accept=".png,.jpg,.jpeg,.webp,.svg,.pdf" class="hidden" onchange="this.form.submit()">
            </form>
            @if($floor->hasPlan())
                <div class="mt-2 text-[11px] text-green-500">✓ Denah sudah diupload ({{ $floor->plan_file_type }})</div>
            @endif
        </div>

        {{-- Ruangan Tersedia (Drag ke Canvas) --}}
        <div>
            <div class="text-[13px] font-bold text-slate-800 border-b border-slate-100 pb-2">📋 Ruangan Tersedia</div>
            <p class="text-[11px] text-slate-400 leading-relaxed mt-1.5">Drag ruangan ke canvas untuk menempatkan marker.</p>
            <div class="flex flex-col gap-1.5 mt-2 max-h-[220px] overflow-y-auto" id="availableRoomsList">
                @forelse($availableRooms as $ar)
                    @php
                        $arColors = ['normal' => 'bg-green-500', 'warning' => 'bg-amber-500', 'poor' => 'bg-red-500'];
                    @endphp
                    <div class="flex items-center gap-2 px-2.5 py-2 rounded-lg bg-blue-50 border border-blue-200 text-[12px] cursor-grab select-none hover:bg-blue-100 transition-colors"
                        draggable="true"
                        id="avail-room-{{ $ar->id }}"
                        data-room-id="{{ $ar->id }}"
                        data-room-name="{{ $ar->name }}"
                        data-room-code="{{ $ar->code }}"
                        data-room-status="{{ $ar->status ?? 'normal' }}"
                        ondragstart="onRoomDragStart(event, this)">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $arColors[$ar->status ?? 'normal'] ?? 'bg-green-500' }}"></span>
                        <span class="flex-1 font-medium text-slate-700 truncate">{{ $ar->name }}</span>
                        <span class="text-[10px] text-slate-400 font-mono shrink-0">{{ $ar->code }}</span>
                    </div>
                @empty
                    <p class="text-[11px] text-slate-400 text-center py-3" id="availableEmptyMsg">Semua ruangan sudah di-assign ke lantai.</p>
                @endforelse
            </div>
        </div>

        {{-- Daftar Ruangan --}}
        <div class="flex-1">
            <div class="text-[13px] font-bold text-slate-800 border-b border-slate-100 pb-2">🏠 Daftar Ruangan (<span id="roomCountLabel">{{ $floor->rooms->count() }}</span>)</div>
            <div class="flex flex-col gap-1.5 mt-2" id="roomListPanel">
                @foreach($floor->rooms as $room)
                <div class="flex items-center gap-2 px-2.5 py-2 rounded-lg bg-slate-50 border border-slate-200 text-[12px]" id="room-list-{{ $room->id }}">
                    <span class="w-2.5 h-2.5 rounded-full shrink-0
                        {{ $room->status === 'normal' ? 'bg-green-500' : ($room->status === 'warning' ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                    <span class="flex-1 font-medium text-slate-700 room-item-name">{{ $room->name }}</span>
                    <button onclick="openEditModal({{ $room->id }}, '{{ addslashes($room->name) }}', '{{ $room->code }}', '{{ $room->status }}', {{ $room->svg_x }}, {{ $room->svg_y }}, {{ $room->svg_width }}, {{ $room->svg_height }})"
                        class="bg-transparent border-none text-slate-400 hover:text-blue-500 cursor-pointer p-0.5 transition-colors flex items-center" title="Edit ruangan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button onclick="deleteRoom({{ $room->id }}, this.closest('[id^=room-list]'))"
                        class="bg-transparent border-none text-slate-400 hover:text-red-500 cursor-pointer p-0.5 transition-colors flex items-center" title="Hapus ruangan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        <div>
            <p class="text-[11px] text-slate-400 leading-relaxed">💡 <strong>Cara edit:</strong> Drag marker di canvas untuk memindahkan ruangan.</p>
        </div>
    </div>

    {{-- ═══ CANVAS AREA ═══ --}}
    <div class="bg-white rounded-xl shadow-[0_1px_4px_rgba(0,0,0,.07)] overflow-hidden relative flex flex-col">

        {{-- Drawing Toolbar --}}
        <div class="flex items-center gap-1.5 px-3 py-2 border-b border-slate-100 bg-slate-50 flex-wrap">
            {{-- Tool buttons --}}
            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-lg p-1">
                <button id="tool-select" onclick="setTool('select')" title="Pilih & Pindah"
                    class="tool-btn active-tool flex items-center gap-1 px-2 py-1 rounded-md text-[11px] font-medium cursor-pointer border-none transition-all">
                    {{-- mouse-pointer --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l7.07 17 2.51-7.39L21 11.07z"/></svg>
                </button>
                <button id="tool-rect" onclick="setTool('rect')" title="Gambar Kotak"
                    class="tool-btn flex items-center gap-1 px-2 py-1 rounded-md text-[11px] font-medium cursor-pointer border-none transition-all">
                    {{-- square --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg>
                </button>
                <button id="tool-line" onclick="setTool('line')" title="Gambar Garis"
                    class="tool-btn flex items-center gap-1 px-2 py-1 rounded-md text-[11px] font-medium cursor-pointer border-none transition-all">
                    {{-- minus / straight line --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <button id="tool-arrow" onclick="setTool('arrow')" title="Gambar Panah"
                    class="tool-btn flex items-center gap-1 px-2 py-1 rounded-md text-[11px] font-medium cursor-pointer border-none transition-all">
                    {{-- arrow-right --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </div>

            <div class="w-px h-5 bg-slate-200"></div>

            {{-- Stroke color --}}
            <div class="flex items-center gap-1.5">
                <span class="text-[11px] text-slate-500">Warna:</span>
                <input type="color" id="strokeColor" value="#334155" title="Warna garis"
                    class="w-7 h-7 rounded cursor-pointer border border-slate-200 p-0.5" onchange="updateActiveColor()">
            </div>

            {{-- Stroke width --}}
            <div class="flex items-center gap-1.5">
                <span class="text-[11px] text-slate-500">Tebal:</span>
                <select id="strokeWidth" onchange="updateActiveWidth()" class="text-[11px] border border-slate-200 rounded px-5 py-0.5 outline-none cursor-pointer">
                    <option value="1">1px</option>
                    <option value="2" selected>2px</option>
                    <option value="3">3px</option>
                    <option value="5">5px</option>
                </select>
            </div>

            <div class="w-px h-5 bg-slate-200"></div>

            {{-- Zoom --}}
            <button onclick="zoomCanvas(1.2)" class="px-2 py-1 bg-white border border-slate-200 rounded-md text-[11px] cursor-pointer hover:bg-slate-50 transition-colors">🔍+</button>
            <button onclick="zoomCanvas(0.8)" class="px-2 py-1 bg-white border border-slate-200 rounded-md text-[11px] cursor-pointer hover:bg-slate-50 transition-colors">🔍−</button>
            <button onclick="resetZoom()" class="px-2 py-1 bg-white border border-slate-200 rounded-md text-[11px] cursor-pointer hover:bg-slate-50 transition-colors">↺</button>
            <span class="text-[11px] text-slate-400"><span id="zoomLevel">100</span>%</span>

            <div class="w-px h-5 bg-slate-200"></div>

            {{-- Delete selected --}}
            <button onclick="deleteSelected()" title="Hapus objek terpilih"
                class="flex items-center gap-1 px-2 py-1 bg-white border border-slate-200 rounded-md text-[11px] text-red-500 cursor-pointer hover:bg-red-50 transition-colors">
                {{-- trash --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </button>

            {{-- Save canvas --}}
            <button onclick="saveCanvas()" title="Simpan drawing ke database"
                class="ml-auto flex items-center gap-1.5 px-3 py-1.5 bg-[#4f7dfc] hover:bg-[#3a65e0] text-white rounded-md text-[11px] font-semibold cursor-pointer border-none transition-colors">
                {{-- save --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan
            </button>

            <span class="ml-auto text-[11px] font-semibold text-red-500" id="modeIndicator"></span>
        </div>

        {{-- Canvas wrapper --}}
        <div class="flex-1 overflow-auto relative bg-slate-200" id="canvasWrapper">
            <canvas id="floor-canvas"></canvas>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center text-slate-400 pointer-events-none" id="canvasHint">
                {{-- upload-cloud --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="block mx-auto mb-2.5 opacity-40"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                <p class="text-[14px] font-semibold text-slate-500">Upload denah terlebih dahulu</p>
                <p class="text-[12px]">PNG, JPG, SVG, atau PDF</p>
            </div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div class="fixed bottom-6 right-6 bg-slate-800 text-white px-[18px] py-2.5 rounded-xl text-[13px] z-[9999] hidden shadow-xl" id="toast"></div>

{{-- ═══ MODAL EDIT RUANGAN ═══ --}}
<div class="hidden fixed inset-0 bg-black/40 z-[1000] items-center justify-center" id="modalEditRoom">
    <div class="bg-white rounded-xl p-6 w-[460px] max-w-[95vw] shadow-2xl">
        <h3 class="text-[16px] font-bold mb-4 flex items-center gap-2">
            <i data-feather="edit-3" class="w-4 h-4 text-[#4f7dfc]"></i> Edit Ruangan
        </h3>
        <input type="hidden" id="editRoomId">
        <div class="grid grid-cols-2 gap-3 mb-3">
            <div class="col-span-2">
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Nama Ruangan *</label>
                <input type="text" id="editRoomName" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-blue-400 box-border">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Kode *</label>
                <input type="text" id="editRoomCode" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-blue-400 box-border uppercase">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Status</label>
                <select id="editRoomStatus" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-blue-400">
                    <option value="normal">✅ Normal</option>
                    <option value="warning">⚠️ Warning</option>
                    <option value="poor">🔴 Poor</option>
                </select>
            </div>
        </div>
        <div class="border-t border-slate-100 pt-3 mb-3">
            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Posisi & Ukuran di Dashboard SVG</div>
            <div class="grid grid-cols-4 gap-2">
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">X</label>
                    <input type="number" id="editSvgX" min="0" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-[12px] outline-none focus:border-blue-400 box-border">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Y</label>
                    <input type="number" id="editSvgY" min="0" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-[12px] outline-none focus:border-blue-400 box-border">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Lebar</label>
                    <input type="number" id="editSvgW" min="10" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-[12px] outline-none focus:border-blue-400 box-border">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-500 mb-1">Tinggi</label>
                    <input type="number" id="editSvgH" min="10" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-[12px] outline-none focus:border-blue-400 box-border">
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-1.5">💡 Nilai ini menentukan posisi & ukuran kotak ruangan di halaman Dashboard.</p>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button type="button" onclick="closeEditModal()"
                class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-md text-[13px] cursor-pointer">Batal</button>
            <button type="button" onclick="saveEditRoom()"
                class="bg-[#4f7dfc] hover:bg-[#3a65e0] text-white border-none px-5 py-2 rounded-lg text-[13px] font-semibold cursor-pointer transition-colors">
                <span id="editSaveBtnLabel">Simpan Perubahan</span>
            </button>
        </div>
    </div>
</div>

{{-- Room data from DB as JSON --}}
@php
    $roomsJson = $floor->rooms->map(fn($r) => [
        'id'       => $r->id,
        'name'     => $r->name,
        'code'     => $r->code,
        'status'   => $r->status,
        'marker_x' => $r->marker_x ?? 50,
        'marker_y' => $r->marker_y ?? 50,
    ])->values()->toJson();
    $planUrl = $floor->plan_url;
    $canvasData = $floor->canvas_data;
@endphp
<script>
const ROOMS_DATA      = {!! $roomsJson !!};
const FLOOR_PLAN_URL  = {!! json_encode($planUrl) !!};
const CANVAS_DATA     = {!! json_encode($canvasData) !!};
const ROUTES = {
    markerUpdate:  '{{ route('admin.rooms.marker.update', ['room' => '__ID__']) }}',
    roomDestroy:   '{{ route('admin.rooms.destroy', ['room' => '__ID__']) }}',
    roomAdd:       '{{ route('admin.floors.rooms.add', $floor) }}',
    roomDetails:   '{{ route('admin.rooms.details.update', ['room' => '__ID__']) }}',
    canvasSave:    '{{ route('admin.floors.canvas.save', $floor) }}',
};
const CSRF = '{{ csrf_token() }}';
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script>
/* ═══════════════════════════════════════════════
FABRIC.JS CANVAS EDITOR — with Drawing Tools
═══════════════════════════════════════════════ */
let canvas, bgImage, currentZoom = 1;
let currentTool = 'select';
let isDrawing = false, drawOrigin = null, activeDrawObj = null;
const STATUS_COLORS = { normal: '#22c55e', warning: '#f59e0b', poor: '#ef4444' };

// Natural canvas dimensions (referensi koordinat marker, sama dengan dashboard)
let _natW = 900, _natH = 560;
let _editorZoom = 1;

/* ── Tool style helpers ── */
function getStrokeColor() { return document.getElementById('strokeColor').value; }
function getStrokeWidth() { return parseInt(document.getElementById('strokeWidth').value); }

/* ── Init ── */
window.addEventListener('load', initCanvas);

function initCanvas() {
    const wrapper = document.getElementById('canvasWrapper');

    // Baca natural size dari canvas_data jika ada
    if (CANVAS_DATA) {
        try {
            const parsed = JSON.parse(CANVAS_DATA);
            if (parsed._canvasWidth)  _natW = parsed._canvasWidth;
            if (parsed._canvasHeight) _natH = parsed._canvasHeight;
        } catch(e) {}
    }

    // Display size = wrapper size
    const dispW = wrapper.clientWidth  || 900;
    const dispH = wrapper.clientHeight || 560;

    // Zoom: scale natural coords ke display
    _editorZoom = Math.min(dispW / _natW, dispH / _natH);
    document.getElementById('zoomLevel').textContent = Math.round(_editorZoom * 100);

    canvas = new fabric.Canvas('floor-canvas', {
        width:  dispW,
        height: dispH,
        selection: true,
        backgroundColor: '#f1f5f9',
    });

    // Viewport transform: semua objek di natural coords
    canvas.setViewportTransform([_editorZoom, 0, 0, _editorZoom, 0, 0]);

    if (FLOOR_PLAN_URL) {
        document.getElementById('canvasHint').classList.add('hidden');
        loadBgImage(FLOOR_PLAN_URL);
    }

    // Load saved canvas drawing data
    if (CANVAS_DATA) {
        const cleanJson = CANVAS_DATA.replace(/"textBaseline"\s*:\s*"alphabetical"/g, '"textBaseline":"alphabetic"');
        canvas.loadFromJSON(cleanJson, () => {
            canvas.renderAll();
            ROOMS_DATA.forEach(r => addMarkerToCanvas(r.id, r.name, r.status, r.marker_x, r.marker_y));
        });
    } else {
        ROOMS_DATA.forEach(r => addMarkerToCanvas(r.id, r.name, r.status, r.marker_x, r.marker_y));
    }

    canvas.on('mouse:down', onMouseDown);
    canvas.on('mouse:move', onMouseMove);
    canvas.on('mouse:up',   onMouseUp);
    canvas.on('object:modified', onMarkerMoved);

    updateToolStyles();
    feather.replace();
    initDropZone();
}

/* ── Tool Selection ── */
function setTool(tool) {
    currentTool = tool;

    if (tool === 'select') {
        canvas.isDrawingMode = false;
        canvas.selection = true;
        canvas.defaultCursor = 'default';
    } else {
        canvas.isDrawingMode = false;
        canvas.selection = false;
        canvas.defaultCursor = 'crosshair';
    }
    updateToolStyles();
}

function updateToolStyles() {
    document.querySelectorAll('.tool-btn').forEach(btn => {
        btn.classList.remove('bg-[#4f7dfc]', 'text-white', 'active-tool');
        btn.classList.add('bg-transparent', 'text-slate-600', 'hover:bg-slate-100');
    });
    const activeBtn = document.getElementById('tool-' + currentTool);
    if (activeBtn) {
        activeBtn.classList.remove('bg-transparent', 'text-slate-600', 'hover:bg-slate-100');
        activeBtn.classList.add('bg-[#4f7dfc]', 'text-white', 'active-tool');
    }
}

function updateActiveColor() {
    const obj = canvas.getActiveObject();
    if (obj) { obj.set('stroke', getStrokeColor()); canvas.renderAll(); }
}
function updateActiveWidth() {
    const obj = canvas.getActiveObject();
    if (obj) { obj.set('strokeWidth', getStrokeWidth()); canvas.renderAll(); }
}

/* ── Mouse Events for Drawing ── */
function onMouseDown(opt) {
    if (currentTool === 'select') return;

    const ptr = canvas.getPointer(opt.e);
    isDrawing = true;
    drawOrigin = { x: ptr.x, y: ptr.y };
    const color = getStrokeColor();
    const width = getStrokeWidth();

    if (currentTool === 'rect') {
        activeDrawObj = new fabric.Rect({
            left: ptr.x, top: ptr.y,
            width: 0, height: 0,
            fill: 'transparent',
            stroke: color, strokeWidth: width,
            selectable: true,
        });
    } else if (currentTool === 'line' || currentTool === 'arrow') {
        activeDrawObj = new fabric.Line([ptr.x, ptr.y, ptr.x, ptr.y], {
            stroke: color, strokeWidth: width,
            selectable: true,
            data: { isArrow: currentTool === 'arrow' },
        });
    }

    if (activeDrawObj) canvas.add(activeDrawObj);
}

function onMouseMove(opt) {
    if (!isDrawing || !activeDrawObj) return;
    const ptr = canvas.getPointer(opt.e);

    if (currentTool === 'rect') {
        const w = ptr.x - drawOrigin.x;
        const h = ptr.y - drawOrigin.y;
        activeDrawObj.set({
            left:   w < 0 ? ptr.x : drawOrigin.x,
            top:    h < 0 ? ptr.y : drawOrigin.y,
            width:  Math.abs(w),
            height: Math.abs(h),
        });
    } else if (currentTool === 'line' || currentTool === 'arrow') {
        activeDrawObj.set({ x2: ptr.x, y2: ptr.y });
    }
    canvas.renderAll();
}

function onMouseUp(opt) {
    if (!isDrawing) return;
    isDrawing = false;

    // If arrow, add arrowhead triangle
    if (currentTool === 'arrow' && activeDrawObj) {
        const x1 = activeDrawObj.x1, y1 = activeDrawObj.y1;
        const x2 = activeDrawObj.x2, y2 = activeDrawObj.y2;
        const angle = Math.atan2(y2 - y1, x2 - x1) * 180 / Math.PI;
        const arrowHead = new fabric.Triangle({
            left: x2, top: y2,
            width: 12, height: 14,
            fill: getStrokeColor(),
            angle: angle + 90,
            originX: 'center', originY: 'center',
            selectable: false, evented: false,
            data: { isArrowHead: true },
        });
        canvas.add(arrowHead);
    }

    activeDrawObj = null;
    canvas.renderAll();
}

/* ── Delete selected ── */
function deleteSelected() {
    const objs = canvas.getActiveObjects();
    objs.forEach(o => {
        // Don't delete room markers
        if (o.data && o.data.roomId) return;
        canvas.remove(o);
    });
    canvas.discardActiveObject();
    canvas.renderAll();
}

/* ── Save canvas to DB ── */
function saveCanvas() {
    // Temporarily remove markers (don't save them in canvas_data)
    const allObjs = canvas.getObjects();
    const markers = allObjs.filter(o => o.data && o.data.roomId);
    markers.forEach(m => canvas.remove(m));

    // Include NATURAL canvas dimensions (referensi koordinat marker untuk dashboard)
    const jsonObj = canvas.toJSON(['data']);
    jsonObj._canvasWidth  = _natW;   // natural, bukan display size
    jsonObj._canvasHeight = _natH;
    const jsonStr = JSON.stringify(jsonObj);

    // Re-add markers
    markers.forEach(m => canvas.add(m));
    canvas.renderAll();

    fetch(ROUTES.canvasSave, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ canvas_data: jsonStr }),
    })
    .then(r => r.json())
    .then(() => showToast('Drawing tersimpan ✓'))
    .catch(() => showToast('Gagal simpan drawing'));
}

/* ── Load bg image ── */
function loadBgImage(url) {
    fabric.Image.fromURL(url, function(img) {
        // Scale to natural canvas size; viewport transform handles display scaling
        const scaleX = _natW / img.width;
        const scaleY = _natH / img.height;
        const scale  = Math.min(scaleX, scaleY);
        img.set({ scaleX: scale, scaleY: scale, selectable: false, evented: false, originX: 'left', originY: 'top' });
        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
        bgImage = img;
    }, { crossOrigin: 'anonymous' });
}

/* ── Room Markers ── */
const STATUS_ICONS = {
    normal:  '{{ asset('icons/normal.svg') }}',
    warning: '{{ asset('icons/warning.svg') }}',
    poor:    '{{ asset('icons/poor.svg') }}',
};
const STATUS_BG = {
    normal:  '#dcfce7',  // green-100
    warning: '#fef3c7',  // amber-100
    poor:    '#fee2e2',  // red-100
};

function addMarkerToCanvas(roomId, roomName, status, xPct, yPct) {
    // Posisi dalam natural coords — viewport transform scale ke display
    const x    = (xPct / 100) * _natW;
    const y    = (yPct / 100) * _natH;
    const bg   = STATUS_BG[status]    || STATUS_BG.normal;
    const iconUrl = STATUS_ICONS[status] || STATUS_ICONS.normal;

    // Marker size dalam natural coords — sama persis dengan dashboard
    const inv      = 1 / _editorZoom;
    const sqSize   = 36 * inv;
    const iconSize = 24 * inv;
    const fSize    = 11 * inv;
    const labelTop = 28 * inv;
    const square = new fabric.Rect({
        width: sqSize, height: sqSize,
        fill: bg, stroke: 'white', strokeWidth: 2 * inv,
        rx: 8 * inv, ry: 8 * inv,
        shadow: new fabric.Shadow({ color: 'rgba(0,0,0,0.20)', blur: 8 * inv, offsetX: 0, offsetY: 3 * inv }),
        originX: 'center', originY: 'center',
    });

    // Room name label below the square
    const label = new fabric.Text(roomName, {
        fontSize: fSize, fill: '#1e293b', fontFamily: 'Inter, sans-serif', fontWeight: '700',
        backgroundColor: 'rgba(255,255,255,0.88)', padding: 2 * inv,
        originX: 'center', originY: 'center',
        top: labelTop,
    });

    // Load status icon as SVG image
    fabric.Image.fromURL(iconUrl, function(icon) {
        icon.set({
            scaleX: iconSize / icon.width,
            scaleY: iconSize / icon.height,
            originX: 'center', originY: 'center',
            top: 0,
        });

        const group = new fabric.Group([square, icon, label], {
            left: x, top: y, originX: 'center', originY: 'center',
            hasControls: false, hasBorders: false,
            data: { roomId, roomName, status },
        });

        canvas.add(group);
        canvas.renderAll();
    }, { crossOrigin: 'anonymous' });
}

// Helper: remove and re-draw marker with updated data
function refreshMarker(roomId, roomName, status) {
    const existing = canvas.getObjects().find(o => o.data && o.data.roomId == roomId);
    if (!existing) return;
    // existing.left/top adalah dalam natural coords
    const xPct = (existing.left / _natW) * 100;
    const yPct = (existing.top  / _natH) * 100;
    canvas.remove(existing);
    addMarkerToCanvas(roomId, roomName, status, xPct, yPct);
}

function onCanvasClick(opt) {
    if (!addMode) return;
    const name   = document.getElementById('newRoomName').value.trim();
    const code   = document.getElementById('newRoomCode').value.trim().toUpperCase();
    const status = document.getElementById('newRoomStatus').value;
    if (!name || !code) { alert('Isi Nama dan Kode ruangan terlebih dahulu.'); return; }

    const ptr  = canvas.getPointer(opt.e);
    // getPointer dengan viewport transform mengembalikan natural coords
    const xPct = (ptr.x / _natW) * 100;
    const yPct = (ptr.y / _natH) * 100;

    fetch(ROUTES.roomAdd, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ name, code, status, marker_x: xPct, marker_y: yPct }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.room) {
            addMarkerToCanvas(data.room.id, data.room.name, data.room.status, xPct, yPct);
            appendRoomListItem(data.room);
            showToast('Ruangan ditambahkan ✓');
        }
    });
    toggleAddMode();
}

function onMarkerMoved(opt) {
    const obj = opt.target;
    if (!obj || !obj.data || !obj.data.roomId) return;
    // obj.left/top dalam natural coords (viewport transform sudah dibalik)
    const xPct = (obj.left / _natW) * 100;
    const yPct = (obj.top  / _natH) * 100;
    const url  = ROUTES.markerUpdate.replace('__ID__', obj.data.roomId);
    fetch(url, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ marker_x: xPct, marker_y: yPct }),
    }).then(() => showToast('Posisi disimpan ✓'));
}

function deleteRoom(roomId, el) {
    if (!confirm('Hapus ruangan ini dari denah?')) return;
    const url = ROUTES.roomDestroy.replace('__ID__', roomId);
    fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(data => {
        if (data.success) {
            // Use == (loose) to avoid type mismatch (string vs integer)
            canvas.getObjects().forEach(o => {
                if (o.data && o.data.roomId == roomId) canvas.remove(o);
            });
            canvas.renderAll();
            if (el) el.remove();
            showToast('Ruangan dihapus ✓');
        }
    })
    .catch(err => {
        console.error('Delete failed:', err);
        showToast('Gagal menghapus ruangan');
    });
}

/* ── Edit Room Modal ── */
function openEditModal(id, name, code, status, svgX, svgY, svgW, svgH) {
    document.getElementById('editRoomId').value     = id;
    document.getElementById('editRoomName').value   = name;
    document.getElementById('editRoomCode').value   = code;
    document.getElementById('editRoomStatus').value = status;
    document.getElementById('editSvgX').value       = svgX;
    document.getElementById('editSvgY').value       = svgY;
    document.getElementById('editSvgW').value       = svgW;
    document.getElementById('editSvgH').value       = svgH;
    document.getElementById('editSaveBtnLabel').textContent = 'Simpan Perubahan';

    const modal = document.getElementById('modalEditRoom');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    // NOTE: feather.replace() NOT called here — icons already replaced on page load.
    // Calling it again causes: TypeError: Failed to execute 'replaceChild' on 'Node'
    // because the <i> elements are already gone (replaced with <svg>).
}

function closeEditModal() {
    const modal = document.getElementById('modalEditRoom');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function saveEditRoom() {
    const id     = document.getElementById('editRoomId').value;
    const name   = document.getElementById('editRoomName').value.trim();
    const code   = document.getElementById('editRoomCode').value.trim().toUpperCase();
    const status = document.getElementById('editRoomStatus').value;
    const svgX   = parseInt(document.getElementById('editSvgX').value) || 0;
    const svgY   = parseInt(document.getElementById('editSvgY').value) || 0;
    const svgW   = parseInt(document.getElementById('editSvgW').value) || 100;
    const svgH   = parseInt(document.getElementById('editSvgH').value) || 80;

    if (!name || !code) { alert('Nama dan Kode tidak boleh kosong.'); return; }

    document.getElementById('editSaveBtnLabel').textContent = 'Menyimpan...';
    const url = ROUTES.roomDetails.replace('__ID__', id);

    fetch(url, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ name, code, status, svg_x: svgX, svg_y: svgY, svg_width: svgW, svg_height: svgH }),
    })
    .then(r => r.json().then(data => ({ ok: r.ok, httpStatus: r.status, data })))
    .then(({ ok, httpStatus, data }) => {
        if (!ok) {
            document.getElementById('editSaveBtnLabel').textContent = 'Simpan Perubahan';
            // Laravel 422 validation errors
            if (httpStatus === 422 && data.errors) {
                const msgs = Object.values(data.errors).flat().join('\n');
                alert('Validasi gagal:\n' + msgs);
            } else {
                alert('Gagal menyimpan (HTTP ' + httpStatus + '). ' + (data.message || ''));
            }
            return;
        }
        if (data.success) {
            // Update room list item in sidebar
            const listItem = document.getElementById('room-list-' + id);
            if (listItem) {
                const dot = listItem.querySelector('span:first-child');
                const nameEl = listItem.querySelector('.room-item-name');
                const dotColors = { normal: 'bg-green-500', warning: 'bg-amber-500', poor: 'bg-red-500' };

                dot.className = 'w-2.5 h-2.5 rounded-full shrink-0 ' + (dotColors[status] || 'bg-green-500');
                if (nameEl) nameEl.textContent = name;

                // Update edit button data attributes
                const editBtn = listItem.querySelector('button[title="Edit ruangan"]');
                if (editBtn) {
                    editBtn.setAttribute('onclick',
                        `openEditModal(${id}, '${name.replace(/'/g, "\\'")}', '${code}', '${status}', ${svgX}, ${svgY}, ${svgW}, ${svgH})`
                    );
                }
            }

            // Re-draw marker with new status icon & square shape
            refreshMarker(id, name, status);

            closeEditModal();
            showToast('Ruangan berhasil diperbarui ✓');
        } else {
            document.getElementById('editSaveBtnLabel').textContent = 'Simpan Perubahan';
            alert('Gagal menyimpan. ' + (data.message || ''));
        }
    })
    .catch(() => {
        document.getElementById('editSaveBtnLabel').textContent = 'Simpan Perubahan';
        alert('Terjadi kesalahan jaringan.');
    });
}

// Close modal on overlay click
document.getElementById('modalEditRoom').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

/* ── Drag & Drop: Ruangan dari panel ke canvas ── */
let _draggedRoom = null;

function onRoomDragStart(event, el) {
    _draggedRoom = {
        id:     el.dataset.roomId,
        name:   el.dataset.roomName,
        code:   el.dataset.roomCode,
        status: el.dataset.roomStatus,
    };
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', el.dataset.roomId);
    el.classList.add('opacity-50');
    setTimeout(() => el.classList.remove('opacity-50'), 0);
}

function initDropZone() {
    const wrapper = document.getElementById('canvasWrapper');

    wrapper.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        wrapper.classList.add('ring-2', 'ring-blue-400', 'ring-inset');
    });

    wrapper.addEventListener('dragleave', function(e) {
        if (!wrapper.contains(e.relatedTarget)) {
            wrapper.classList.remove('ring-2', 'ring-blue-400', 'ring-inset');
        }
    });

    wrapper.addEventListener('drop', function(e) {
        e.preventDefault();
        wrapper.classList.remove('ring-2', 'ring-blue-400', 'ring-inset');
        if (!_draggedRoom) return;

        // Hitung posisi drop relatif ke canvas element
        const canvasEl = document.getElementById('floor-canvas');
        const rect     = canvasEl.getBoundingClientRect();
        const clientX  = e.clientX - rect.left;
        const clientY  = e.clientY - rect.top;

        // Transform ke natural canvas coords (bagi viewport transform zoom)
        const vpt  = canvas.viewportTransform;
        const natX = (clientX - vpt[4]) / vpt[0];
        const natY = (clientY - vpt[5]) / vpt[3];

        const xPct = Math.max(0, Math.min(100, (natX / _natW) * 100));
        const yPct = Math.max(0, Math.min(100, (natY / _natH) * 100));

        const room = { ..._draggedRoom };
        _draggedRoom = null;

        fetch(ROUTES.roomAdd, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ room_id: room.id, marker_x: xPct, marker_y: yPct }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.room) {
                addMarkerToCanvas(data.room.id, data.room.name, data.room.status, xPct, yPct);
                appendRoomListItem(data.room);
                removeFromAvailableList(data.room.id);
                showToast('Ruangan "' + data.room.name + '" ditempatkan ✓');
            }
        })
        .catch(() => showToast('Gagal menempatkan ruangan'));
    });
}

/* ── Zoom ── */
function zoomCanvas(factor) {
    currentZoom = Math.min(4, Math.max(0.3, currentZoom * factor));
    canvas.setZoom(currentZoom);
    canvas.setDimensions({ width: canvas.width, height: canvas.height });
    document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100);
}
function resetZoom() {
    currentZoom = 1;
    canvas.setZoom(1);
    canvas.setViewportTransform([1,0,0,1,0,0]);
    document.getElementById('zoomLevel').textContent = 100;
}

/* ── Toast ── */
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.remove('hidden');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.add('hidden'), 2500);
}

/* ── Append room list item (after placing room on canvas) ── */
function appendRoomListItem(room) {
    const dotColors = { normal: 'bg-green-500', warning: 'bg-amber-500', poor: 'bg-red-500' };
    const li = document.createElement('div');
    li.className = 'flex items-center gap-2 px-2.5 py-2 rounded-lg bg-slate-50 border border-slate-200 text-[12px]';
    li.id = 'room-list-' + room.id;
    li.innerHTML = `
        <span class="w-2.5 h-2.5 rounded-full shrink-0 ${dotColors[room.status] || 'bg-green-500'}"></span>
        <span class="flex-1 font-medium text-slate-700 room-item-name">${room.name}</span>
        <button onclick="openEditModal(${room.id}, '${room.name.replace(/'/g,"\\'")  }', '${room.code}', '${room.status}', 0, 0, 100, 80)"
            class="bg-transparent border-none text-slate-400 hover:text-blue-500 cursor-pointer p-0 transition-colors" title="Edit ruangan">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </button>
        <button onclick="deleteRoom(${room.id}, this.closest('[id^=room-list]'))"
            class="bg-transparent border-none text-slate-400 hover:text-red-500 cursor-pointer p-0 transition-colors" title="Hapus ruangan">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        </button>
    `;
    document.getElementById('roomListPanel').appendChild(li);
    // Update jumlah ruangan
    const cnt = document.getElementById('roomCountLabel');
    if (cnt) cnt.textContent = parseInt(cnt.textContent || 0) + 1;
    const cnt2 = document.getElementById('roomCount');
    if (cnt2) cnt2.textContent = parseInt(cnt2.textContent || 0) + 1;
}

/* ── Hapus dari daftar "Ruangan Tersedia" setelah di-drop ── */
function removeFromAvailableList(roomId) {
    const el = document.getElementById('avail-room-' + roomId);
    if (el) el.remove();
    // Tampilkan pesan kosong jika tidak ada lagi
    const list = document.getElementById('availableRoomsList');
    if (list && list.children.length === 0) {
        const msg = document.createElement('p');
        msg.className = 'text-[11px] text-slate-400 text-center py-3';
        msg.id = 'availableEmptyMsg';
        msg.textContent = 'Semua ruangan sudah di-assign ke lantai.';
        list.appendChild(msg);
    }
}
</script>
@endsection
