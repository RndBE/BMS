{{-- Tab: Aturan Peringatan --}}
<div class="flex justify-between items-center mb-5">
    <p class="text-[16px] font-bold text-slate-800 dark:text-slate-200">Daftar Peringatan</p>
    <div class="flex items-center gap-3">
        {{-- Search --}}
        <div class="relative">
            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="search-rule" placeholder="Cari peringatan..."
                class="pl-8 pr-3 py-[7px] border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 transition-colors w-52">
        </div>
        <button id="btn-add-rule"
                class="flex items-center gap-2 px-4 py-[7px] bg-red-700 text-white text-[12.5px] font-semibold rounded-lg hover:bg-red-800 transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Peringatan
        </button>
    </div>
</div>

{{-- Rules Table --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden dark:border-[#1D1D1D] dark:bg-[#2a2a2a] dark:text-white">
    @if($rules->isEmpty())
        <div class="py-16 flex flex-col items-center gap-3 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" class="text-slate-300"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <p class="text-[13px] font-medium">Belum ada aturan peringatan</p>
            <p class="text-[12px]">Klik "Tambah Peringatan" untuk membuat aturan baru.</p>
        </div>
    @else
        <table class="w-full text-[13px]" id="tbl-rules">
            <thead class="bg-red-50 border-b border-red-100 dark:border-[#1D1D1D] dark:bg-[#1D1D1D] ">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Nama Peringatan</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Kategori</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Ruangan</th>
                    <th class="px-5 py-3 text-center font-semibold text-slate-800 dark:text-slate-200">Aktif</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Durasi Tunda</th>
                    <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Status</th>
                    <th class="px-5 py-3 text-right font-semibold text-slate-800 dark:text-slate-200">Aksi</th>
                </tr>
            </thead>
            <tbody id="rules-tbody" class="divide-y divide-slate-50 dark:divide-[#1D1D1D]">
                @foreach($rules as $rule)
                @php
                    $paramLabels = [
                        'suhu' => 'Suhu', 'kelembaban' => 'Kelembaban',
                        'co2' => 'CO₂', 'daya' => 'Daya', 'tegangan' => 'Tegangan',
                    ];
                    $paramLabel = $paramLabels[$rule->parameter_key] ?? $rule->parameter_key;
                    $units = ['suhu' => '°C', 'kelembaban' => '%', 'co2' => 'ppm', 'daya' => 'kW', 'tegangan' => 'V'];
                    $unit = $units[$rule->parameter_key] ?? '';
                @endphp
                <tr class="rule-row hover:bg-slate-50 dark:bg-[#232323] dark:hover:bg-transparent transition-colors" data-id="{{ $rule->id }}"
                    data-name="{{ $rule->name }}">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $rule->name }}</p>
                        <p class="text-[11.5px] text-slate-400 mt-0.5">Pemicu: {{ $paramLabel }} {{ $rule->condition }} {{ $rule->threshold }}{{ $unit }}</p>
                    </td>
                    <td class="px-5 py-3">
                        @if($rule->kategori)
                            <span class="text-[12px] text-blue-500 font-medium hover:underline cursor-pointer">{{ $rule->kategori }}</span>
                        @else
                            <span class="text-[12px] text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if(empty($rule->room_ids))
                            <span class="text-[11.5px] text-slate-400 italic">Semua Ruangan</span>
                        @else
                            <div class="flex flex-wrap gap-1">
                                @foreach($rooms->whereIn('id', $rule->room_ids ?? []) as $r)
                                    <span class="inline-block bg-red-50 text-red-600 text-[11px] font-medium px-2 py-0.5 rounded-full">{{ $r->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        <button onclick="toggleRule({{ $rule->id }}, this)"
                                class="w-10 h-5 rounded-full transition-colors relative {{ $rule->is_active ? 'bg-green-500' : 'bg-slate-300' }}"
                                data-active="{{ $rule->is_active ? '1' : '0' }}">
                            <span class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-all {{ $rule->is_active ? 'left-5' : 'left-0.5' }}"></span>
                        </button>
                    </td>
                    <td class="px-5 py-3 text-slate-600 dark:text-slate-200">
                        {{ $rule->durasi_tunda ? $rule->durasi_tunda . ' menit' : '—' }}
                    </td>
                    <td class="px-5 py-3">
                        @if($rule->severity === 'critical')
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-red-600 bg-red-50 rounded-full px-2.5 py-0.5">
                                <img src="{{ asset('icons/poor.svg') }}" alt="Poor" class="w-4 h-4 shrink-0"> Poor
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 bg-orange-50 rounded-full px-2.5 py-0.5">
                                <img src="{{ asset('icons/warning.svg') }}" alt="Warning" class="w-4 h-4 shrink-0"> Warning
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <button class="btn-edit-rule text-slate-400 hover:text-blue-600 transition-colors cursor-pointer"
                                data-id="{{ $rule->id }}"
                                data-name="{{ $rule->name }}"
                                data-kategori="{{ $rule->kategori }}"
                                data-parameter_key="{{ $rule->parameter_key }}"
                                data-condition="{{ $rule->condition }}"
                                data-threshold="{{ $rule->threshold }}"
                                data-severity="{{ $rule->severity }}"
                                data-notification_channel="{{ $rule->notification_channel }}"
                                data-durasi_tunda="{{ $rule->durasi_tunda }}"
                                data-room_ids="{{ json_encode($rule->room_ids ?? []) }}"
                                data-is_active="{{ $rule->is_active ? '1' : '0' }}">
                            <img src="{{ asset('icons/edit.svg') }}" alt="Edit" class="w-7 h-7">
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Modal Tambah/Edit Aturan --}}
<div id="modal-rule" class="hidden fixed inset-0 bg-black/40 z-[200] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#232323] rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-[#2d2d2d] sticky top-0 bg-white dark:bg-[#232323] z-10">
            <h2 id="modal-rule-title" class="text-[16px] font-bold text-slate-800 dark:text-white">Tambah Peringatan</h2>
            <button onclick="closeRuleModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="form-rule" class="px-6 py-5 space-y-4">
            <input type="hidden" id="rule-id">

            {{-- Row 1: Nama + Kategori --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 dark:text-slate-200 mb-1.5">Nama Peringatan</label>
                    <input id="rule-name" type="text" placeholder="e.g. Suhu Tinggi"
                        class="w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] focus:outline-none focus:border-red-400 transition-colors">
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 dark:text-slate-200 mb-1.5">Kategori</label>
                    <div class="relative custom-select-wrapper w-full">
                        <select id="rule-kategori" class="hidden real-select">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Kenyamanan">Kenyamanan</option>
                            <option value="Keamanan">Keamanan</option>
                            <option value="Efisiensi">Efisiensi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer transition-colors">
                            <span class="select-text truncate text-left">-- Pilih Kategori --</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute top-[100%] left-0 w-full mt-1 bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-[1100] text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                </div>
            </div>

            {{-- Ruangan --}}
            <div>
                <label class="block text-[12px] font-medium text-slate-600 dark:text-slate-200 mb-2">Ruangan</label>
                <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto pr-1">
                    @foreach($rooms as $room)
                    <label class="flex items-center gap-2 px-3 py-2 border border-slate-200 dark:border-[#3d3d3d] dark:hover:bg-[#2a2a2a] rounded-lg hover:bg-slate-50 cursor-pointer transition-colors">
                        <input type="checkbox" class="rule-room-chk w-3.5 h-3.5 accent-red-600 shrink-0"
                               value="{{ $room->id }}">
                        <span class="text-[12.5px] text-slate-700 dark:text-slate-300 truncate">{{ $room->name }}</span>
                    </label>
                    @endforeach
                    @if($rooms->isEmpty())
                        <p class="text-[12px] text-slate-200 col-span-2">Tidak ada ruangan tersedia.</p>
                    @endif
                </div>
                <p class="mt-1.5 text-[11.5px] text-slate-400">Kosongkan untuk berlaku di <strong>semua ruangan</strong>.</p>
            </div>

            {{-- Status Keaktifan --}}
            <div class="flex items-center justify-between py-1">
                <label class="text-[12.5px] font-medium text-slate-600 dark:text-slate-200">Status Keaktifan</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input id="rule-active" type="checkbox" class="sr-only peer" checked>
                    <div class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:bg-green-500 transition-colors"></div>
                    <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-all peer-checked:translate-x-5"></span>
                </label>
            </div>

            {{-- Durasi Tunda + Status --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 dark:text-slate-200 mb-1.5">Durasi Tunda</label>
                    <div class="relative">
                        <input id="rule-durasi" type="number" min="0" placeholder="e.g. 15"
                               class="w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 pr-14 text-[13px] focus:outline-none focus:border-red-400 transition-colors">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[12px] text-slate-700 dark:text-slate-300 select-none">menit</span>
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 dark:text-slate-200 mb-1.5">Status</label>
                    <div class="relative custom-select-wrapper w-full">
                        <select id="rule-severity" class="hidden real-select">
                            <option value="warning">Warning</option>
                            <option value="critical">Poor</option>
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer transition-colors">
                            <span class="select-text truncate text-left">Warning</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute bottom-[100%] mb-1 left-0 w-full bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-[1100] text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                </div>
            </div>

            {{-- Parameter + Operator + Nilai --}}
            <div>
                <label class="block text-[12px] font-medium text-slate-600 mb-1.5">Parameter, Operator & Nilai</label>
                <div class="flex items-center gap-2">
                    <div class="relative custom-select-wrapper flex-1">
                        <select id="rule-param" class="hidden real-select">
                            <option value="suhu">Suhu</option>
                            <option value="kelembaban">Kelembaban</option>
                            <option value="co2">CO₂</option>
                            <option value="daya">Daya</option>
                            <option value="tegangan">Tegangan</option>
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer transition-colors">
                            <span class="select-text truncate text-left">Suhu</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 pointer-events-none ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute bottom-[100%] mb-1 left-0 w-full bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-[1100] text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                    <div class="relative custom-select-wrapper w-24">
                        <select id="rule-condition" class="hidden real-select">
                            <option value=">">&gt;</option>
                            <option value="<">&lt;</option>
                            <option value=">=">&ge;</option>
                            <option value="<=">&le;</option>
                        </select>
                        <button type="button" class="select-btn flex items-center justify-between w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3 py-2.5 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-red-400 cursor-pointer transition-colors">
                            <span class="select-text truncate text-left">&gt;</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 pointer-events-none ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul class="select-dropdown absolute bottom-[100%] mb-1 left-0 w-full bg-white dark:bg-[#2a2a2a] border border-slate-200 dark:border-[#3d3d3d] rounded-lg shadow-lg hidden max-h-60 overflow-y-auto py-1 z-[1100] text-[13px] text-slate-700 dark:text-slate-200"></ul>
                    </div>
                    <div class="relative w-32">
                        <input id="rule-threshold" type="number" step="any" placeholder="28"
                               class="w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 pr-10 text-[13px] focus:outline-none focus:border-red-400 transition-colors">
                        <span id="rule-unit" class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] text-slate-700 dark:text-slate-300 select-none">°C</span>
                    </div>
                </div>
            </div>

            <div id="rule-error" class="hidden text-[12px] text-red-500 bg-red-50 rounded-lg px-3 py-2"></div>

            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100 dark:border-[#2d2d2d]">
                <button type="button" onclick="closeRuleModal()"
                        class="px-5 py-2.5 rounded-lg border border-slate-300 dark:border-[#3d3d3d] dark:text-slate-300 dark:hover:bg-[#2a2a2a] text-[13px] font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-7 py-2.5 bg-red-700 text-white text-[13px] font-semibold rounded-lg hover:bg-red-800 transition-colors shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast --}}
<div id="toast-rule" class="hidden fixed bottom-6 right-6 z-50 bg-green-600 text-white text-[13px] font-medium px-5 py-3 rounded-xl shadow-lg">
    Berhasil disimpan.
</div>

@push('scripts')
<script>
(function () {
    const modal     = document.getElementById('modal-rule');
    const form      = document.getElementById('form-rule');
    const toast     = document.getElementById('toast-rule');
    const errBox    = document.getElementById('rule-error');
    let   editingId = null;

    // Unit map
    const units = { suhu: '°C', kelembaban: '%', co2: 'ppm', daya: 'kW', tegangan: 'V' };
    document.getElementById('rule-param').addEventListener('change', function () {
        document.getElementById('rule-unit').textContent = units[this.value] ?? '';
    });

    // ── Helpers ──
    function showToast(msg, ok = true) {
        toast.textContent = msg;
        toast.classList.remove('hidden', 'bg-green-600', 'bg-red-600');
        toast.classList.add(ok ? 'bg-green-600' : 'bg-red-600');
        setTimeout(() => toast.classList.add('hidden'), 3000);
    }

    function openModal()  { modal.classList.remove('hidden'); }
    window.closeRuleModal = function () { modal.classList.add('hidden'); };

    function setRoomCheckboxes(ids) {
        document.querySelectorAll('.rule-room-chk').forEach(chk => {
            chk.checked = ids.includes(parseInt(chk.value));
        });
    }

    // ── Open Add Modal ──
    document.getElementById('btn-add-rule').addEventListener('click', () => {
        editingId = null;
        document.getElementById('modal-rule-title').textContent = 'Tambah Peringatan';
        form.reset();
        document.getElementById('rule-active').checked = true;
        document.getElementById('rule-unit').textContent = '°C';
        setRoomCheckboxes([]);
        errBox.classList.add('hidden');
        openModal();
    });

    // ── Open Edit Modal ──
    document.querySelectorAll('.btn-edit-rule').forEach(btn => {
        btn.addEventListener('click', () => {
            const d = btn.dataset;
            editingId = d.id;
            document.getElementById('modal-rule-title').textContent = 'Edit Peringatan';
            document.getElementById('rule-name').value         = d.name;
            document.getElementById('rule-kategori').value     = d.kategori || '';
            document.getElementById('rule-param').value        = d.parameter_key;
            document.getElementById('rule-condition').value    = d.condition;
            document.getElementById('rule-threshold').value    = d.threshold;
            document.getElementById('rule-severity').value     = d.severity;
            document.getElementById('rule-durasi').value       = d.durasi_tunda || '';
            document.getElementById('rule-active').checked     = d.is_active === '1';
            document.getElementById('rule-unit').textContent   = units[d.parameter_key] ?? '';
            const roomIds = JSON.parse(d.room_ids || '[]').map(Number);
            setRoomCheckboxes(roomIds);
            errBox.classList.add('hidden');
            openModal();
        });
    });

    // ── Search ──
    document.getElementById('search-rule')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.rule-row').forEach(row => {
            const name = row.dataset.name.toLowerCase();
            row.style.display = name.includes(q) ? '' : 'none';
        });
    });

    // ── Form Submit ──
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errBox.classList.add('hidden');

        const roomIds = [...document.querySelectorAll('.rule-room-chk:checked')].map(c => parseInt(c.value));

        const body = {
            name:                 document.getElementById('rule-name').value,
            kategori:             document.getElementById('rule-kategori').value,
            parameter_key:        document.getElementById('rule-param').value,
            condition:            document.getElementById('rule-condition').value,
            threshold:            document.getElementById('rule-threshold').value,
            severity:             document.getElementById('rule-severity').value,
            durasi_tunda:         document.getElementById('rule-durasi').value || null,
            room_ids:             roomIds,
            is_active:            document.getElementById('rule-active').checked ? 1 : 0,
        };

        const url    = editingId
            ? `/pengaturan/peringatan/rules/${editingId}`
            : '{{ route('pengaturan.peringatan.rules.store') }}';
        const method = editingId ? 'PUT' : 'POST';

        const res  = await fetch(url, {
            method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        });
        const json = await res.json();

        if (!res.ok) {
            const msgs = json.errors ? Object.values(json.errors).flat().join(' ') : 'Terjadi kesalahan.';
            errBox.textContent = msgs;
            errBox.classList.remove('hidden');
            return;
        }

        closeRuleModal();
        showToast('Peringatan berhasil disimpan.');
        setTimeout(() => location.reload(), 800);
    });

    // ── Delete (via delete button if added later) ──
    window.deleteRule = async function (id) {
        if (!confirm('Hapus aturan ini?')) return;
        const res  = await fetch(`/pengaturan/peringatan/rules/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json',
            },
        });
        const json = await res.json();
        if (json.success) {
            showToast('Aturan dihapus.');
            setTimeout(() => location.reload(), 600);
        }
    };

    // ── Toggle ──
    window.toggleRule = async function (id, btn) {
        const res  = await fetch(`/pengaturan/peringatan/rules/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json',
            },
        });
        const json = await res.json();
        if (json.success) {
            const active = json.is_active;
            btn.dataset.active = active ? '1' : '0';
            btn.classList.toggle('bg-green-500', active);
            btn.classList.toggle('bg-slate-300', !active);
            btn.querySelector('span').classList.toggle('left-5', active);
            btn.querySelector('span').classList.toggle('left-0.5', !active);
        }
    };

    // ── Close on backdrop ──
    modal.addEventListener('click', (e) => { if (e.target === modal) closeRuleModal(); });
})();
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
