{{-- Tab: Permission --}}

{{-- Toolbar --}}
<div class="flex items-center justify-between mb-5">
    <h2 class="text-[16px] font-bold text-slate-800">Daftar Permission</h2>
    <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('pengaturan.pengguna') }}" class="flex items-center">
            <input type="hidden" name="tab" value="permission">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari permission..."
                    class="pl-8 pr-3 py-[7px] border border-slate-200 rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 w-52">
            </div>
        </form>
        <button id="btn-add-perm"
            class="flex items-center gap-1.5 bg-red-700 hover:bg-red-800 text-white text-[12.5px] font-semibold px-4 py-[8px] rounded-lg transition-colors cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Permission
        </button>
    </div>
</div>

{{-- Table --}}
<div class="rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-[13px]">
        <thead class="bg-red-100 border-b border-slate-200">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-slate-800 w-8">No</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-800">Nama Permission</th>
                <th class="px-5 py-3 text-center font-semibold text-slate-800">Guard</th>
                <th class="px-5 py-3 text-center font-semibold text-slate-800">Roles</th>
                <th class="px-5 py-3 text-right font-semibold text-slate-800">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($permissions as $perm)
            <tr class="border-b border-slate-100 hover:bg-slate-50/60 transition-colors">
                <td class="px-5 py-3 text-slate-400">{{ $permissions->firstItem() + $loop->index }}</td>
                <td class="px-5 py-3">
                    <span class="font-mono text-[12.5px] font-semibold text-slate-700 px-2.5 py-1 rounded-md">{{ str_replace('_', ' ', $perm->name) }}</span>
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600">{{ $perm->guard_name }}</span>
                </td>
                <td class="px-5 py-3 text-center text-slate-500">{{ $perm->roles_count }}</td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button class="btn-edit-perm p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors"
                                data-id="{{ $perm->id }}"
                                data-name="{{ str_replace('_', ' ', $perm->name) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button onclick="deletePerm({{ $perm->id }})"
                                class="p-1.5 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" class="mx-auto mb-2 text-slate-300"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <p class="text-[13px] font-medium">Belum ada permission</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($permissions->hasPages())
<div class="mt-4">{{ $permissions->links() }}</div>
@endif

{{-- ═══════════════ MODAL TAMBAH/EDIT PERMISSION ═══════════════ --}}
<div id="modal-perm" class="hidden fixed inset-0 bg-black/40 z-[200] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 id="modal-perm-title" class="text-[15px] font-semibold text-slate-800">Tambah Permission</h2>
            <button onclick="closePermModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="form-perm" class="px-6 py-1 space-y-2">
            <input type="hidden" id="perm-id">
            <div>
                <label class="block text-[12px] font-medium text-slate-600 mb-1.5">Nama Permission</label>
                <input id="perm-name" type="text" placeholder="e.g. view-dashboard"
                       class="w-full border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] font-mono focus:outline-none focus:border-red-400 transition-colors">
            </div>
            <div id="perm-error" class="hidden text-[12px] text-red-500 bg-red-50 rounded-lg px-3 py-2"></div>
            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closePermModal()"
                        class="px-5 py-2.5 rounded-lg border border-slate-300 text-[13px] font-medium text-slate-600 hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit"
                        class="px-6 py-2.5 bg-red-600 text-white text-[13px] font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Toast --}}
<div id="toast-perm" class="hidden fixed bottom-6 right-6 z-50 text-white text-[13px] font-medium px-5 py-3 rounded-xl shadow-lg"></div>

@push('scripts')
<script>
(function () {
    const modal   = document.getElementById('modal-perm');
    const form    = document.getElementById('form-perm');
    const errBox  = document.getElementById('perm-error');
    const toast   = document.getElementById('toast-perm');
    let   editing = null;

    function showToast(msg, ok = true) {
        toast.textContent = msg;
        toast.className = 'fixed bottom-6 right-6 z-50 text-white text-[13px] font-medium px-5 py-3 rounded-xl shadow-lg '
                        + (ok ? 'bg-green-600' : 'bg-red-600');
        setTimeout(() => toast.className += ' hidden', 3000);
    }

    window.closePermModal = () => modal.classList.add('hidden');

    document.getElementById('btn-add-perm').addEventListener('click', () => {
        editing = null;
        document.getElementById('modal-perm-title').textContent = 'Tambah Permission';
        form.reset();
        errBox.classList.add('hidden');
        modal.classList.remove('hidden');
    });

    // Edit via data attributes
    document.querySelectorAll('.btn-edit-perm').forEach(btn => {
        btn.addEventListener('click', function () {
            editing = this.dataset.id;
            document.getElementById('modal-perm-title').textContent = 'Edit Permission';
            document.getElementById('perm-id').value   = this.dataset.id;
            document.getElementById('perm-name').value = this.dataset.name;
            errBox.classList.add('hidden');
            modal.classList.remove('hidden');
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errBox.classList.add('hidden');
        const body   = { name: document.getElementById('perm-name').value };
        const url    = editing ? `/pengaturan/pengguna/permissions/${editing}` : '{{ route('pengaturan.pengguna.permissions.store') }}';
        const method = editing ? 'PUT' : 'POST';
        const res    = await fetch(url, {
            method,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const json = await res.json();
        if (!res.ok) { errBox.textContent = Object.values(json.errors || {}).flat().join(' '); errBox.classList.remove('hidden'); return; }
        closePermModal();
        showToast('Permission berhasil disimpan.');
        setTimeout(() => location.reload(), 700);
    });

    window.deletePerm = async (id) => {
        if (!confirm('Hapus permission ini?')) return;
        const res  = await fetch(`/pengaturan/pengguna/permissions/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
        });
        const json = await res.json();
        if (json.success) { showToast('Permission dihapus.'); setTimeout(() => location.reload(), 600); }
    };

    modal.addEventListener('click', (e) => { if (e.target === modal) closePermModal(); });
})();
</script>
@endpush
