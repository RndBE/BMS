{{-- Tab: Permission --}}

{{-- Toolbar --}}
<div class="flex items-center justify-between mb-5">
    <h2 class="text-[16px] font-bold text-slate-800 dark:text-slate-200">Daftar Permission</h2>
    <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('pengaturan.pengguna') }}" class="flex items-center">
            <input type="hidden" name="tab" value="permission">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari permission..."
                    class="pl-8 pr-3 py-[7px] border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 w-52">
            </div>
        </form>
        <button type="button" id="btn-add-perm"
            class="flex items-center gap-1.5 bg-red-700 hover:bg-red-800 text-white text-[12.5px] font-semibold px-4 py-[7px] rounded-lg transition-colors cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Permission
        </button>
    </div>
</div>

{{-- Table --}}
<div class="rounded-xl border border-slate-200 overflow-hidden dark:border-[#1D1D1D]">
    <table class="w-full text-[13px]">
        <thead class="bg-red-100 border-b border-slate-200 dark:border-[#1D1D1D] dark:bg-[#1D1D1D]">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-slate-800 w-8 dark:text-slate-200">No</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Nama Permission</th>
                <th class="px-5 py-3 text-center font-semibold text-slate-800 dark:text-slate-200">Guard</th>
                <th class="px-5 py-3 text-center font-semibold text-slate-800 dark:text-slate-200">Roles</th>
                <th class="px-5 py-3 text-right font-semibold text-slate-800 dark:text-slate-200">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 dark:divide-[#1D1D1D]">
            @forelse($permissions as $perm)
            <tr class="hover:bg-slate-50/60 dark:hover:bg-transparent transition-colors">
                <td class="px-5 py-3 text-slate-400">{{ $permissions->firstItem() + $loop->index }}</td>
                <td class="px-5 py-3">
                    <span class="font-mono text-[12.5px] font-semibold text-slate-700 px-2.5 py-1 rounded-md dark:text-slate-200">{{ str_replace('_', ' ', $perm->name) }}</span>
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600">{{ $perm->guard_name }}</span>
                </td>
                <td class="px-5 py-3 text-center text-slate-500 dark:text-slate-200">{{ $perm->roles_count }}</td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button class="btn-edit-perm text-slate-400 hover:text-blue-600 transition-colors cursor-pointer"
                                data-id="{{ $perm->id }}"
                                data-name="{{ str_replace('_', ' ', $perm->name) }}">
                            <img src="{{ asset('icons/edit.svg') }}" alt="Edit" class="w-7 h-7">
                        </button>
                        <button onclick="deletePerm({{ $perm->id }}, '{{ addslashes(str_replace('_', ' ', $perm->name)) }}')"
                                class="text-slate-400 hover:text-red-600 transition-colors cursor-pointer">
                            <img src="{{ asset('icons/hapus.svg') }}" alt="Delete" class="w-7 h-7">
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

{{-- Pagination --}}
<div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-50 dark:border-[#1D1D1D]">
    <span class="text-[12px] text-slate-400">
        Menampilkan {{ $permissions->firstItem() ?? 0 }} – {{ $permissions->lastItem() ?? 0 }} dari {{ $permissions->total() }} data
    </span>
    <div class="flex items-center gap-1">
        @if($permissions->onFirstPage())
            <span class="w-7 h-7 flex items-center justify-center rounded text-slate-300 cursor-not-allowed">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </span>
        @else
            <a href="{{ $permissions->previousPageUrl() }}&tab=permission&search={{ $search }}"
                class="w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:bg-slate-100 no-underline transition-colors">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
        @endif

        @foreach($permissions->getUrlRange(1, $permissions->lastPage()) as $page => $url)
            <a href="{{ $url }}&tab=permission&search={{ $search }}"
                class="w-7 h-7 flex items-center justify-center rounded text-[12px] font-medium no-underline transition-colors
                    {{ $page == $permissions->currentPage()
                        ? 'bg-red-700 text-white dark:border-[#FDEBEB] dark:bg-[#FDEBEB] dark:text-black'
                        : 'text-slate-500 hover:bg-slate-100' }}">
                {{ $page }}
            </a>
        @endforeach

        @if($permissions->hasMorePages())
            <a href="{{ $permissions->nextPageUrl() }}&tab=permission&search={{ $search }}"
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

{{-- ═══════════════ MODAL TAMBAH/EDIT PERMISSION ═══════════════ --}}
<div id="modal-perm" class="hidden fixed inset-0 bg-black/40 z-[200] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#232323] rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-[#2d2d2d]">
            <h2 id="modal-perm-title" class="text-[15px] font-semibold text-slate-800 dark:text-white">Tambah Permission</h2>
            <button onclick="closePermModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="form-perm" class="px-6 py-1 space-y-2">
            <input type="hidden" id="perm-id">
            <div>
                <label class="block text-[12px] font-medium text-slate-600 dark:text-slate-400 mb-1.5">Nama Permission</label>
                <input id="perm-name" type="text" placeholder="e.g. view-dashboard"
                       class="w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] font-mono focus:outline-none focus:border-red-400 transition-colors">
            </div>
            <div id="perm-error" class="hidden text-[12px] text-red-500 bg-red-50 rounded-lg px-3 py-2"></div>
            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100 dark:border-[#2d2d2d]">
                <button type="button" onclick="closePermModal()"
                        class="px-5 py-2.5 rounded-lg border border-slate-300 dark:border-[#3d3d3d] dark:text-slate-300 dark:hover:bg-[#2a2a2a] text-[13px] font-medium text-slate-600 hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit"
                        class="px-6 py-2.5 bg-red-600 text-white text-[13px] font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Toast --}}
<div id="toast-perm" class="hidden fixed bottom-6 right-6 z-50 text-white text-[13px] font-medium px-5 py-3 rounded-xl shadow-lg"></div>

{{-- Modal Konfirmasi Hapus Permission --}}
<div id="modal-delete-perm" class="hidden fixed inset-0 bg-black/50 z-[300] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#232323] rounded-2xl shadow-2xl w-full max-w-sm text-center overflow-hidden">
        <div class="px-8 pt-8 pb-6">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('icons/delete.svg') }}" alt="Hapus" class="w-12 h-12">
            </div>
            <h3 class="text-[16px] font-bold text-slate-800 dark:text-white mb-2">Hapus Permission</h3>
            <p class="text-[13px] text-slate-500 dark:text-slate-400">
                Anda yakin ingin menghapus permission <strong id="delete-perm-name" class="text-slate-700 dark:text-slate-200"></strong>?
                <br>Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div class="flex justify-center gap-3 px-8 pb-7">
            <button onclick="closeDeletePermModal()"
                class="px-7 py-2.5 rounded-lg border border-slate-300 dark:border-[#FFFFFF] dark:text-[#FFFFFF] text-[13px] font-medium text-slate-600 hover:bg-slate-50 dark:hover:bg-[#2a2a2a] transition-colors cursor-pointer bg-white dark:bg-transparent">
                Batal
            </button>
            <button onclick="confirmDeletePerm()"
                class="px-7 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-[13px] font-semibold transition-colors cursor-pointer">
                Hapus
            </button>
        </div>
    </div>
</div>

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

    let deletingPermId = null;

    window.deletePerm = (id, name) => {
        deletingPermId = id;
        document.getElementById('delete-perm-name').textContent = name;
        document.getElementById('modal-delete-perm').classList.remove('hidden');
    };

    window.closeDeletePermModal = () => {
        deletingPermId = null;
        document.getElementById('modal-delete-perm').classList.add('hidden');
    };

    window.confirmDeletePerm = async () => {
        if (!deletingPermId) return;
        const res  = await fetch(`/pengaturan/pengguna/permissions/${deletingPermId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
        });
        const json = await res.json();
        closeDeletePermModal();
        if (json.success) { showToast('Permission dihapus.'); setTimeout(() => location.reload(), 600); }
    };

    document.getElementById('modal-delete-perm').addEventListener('click', function(e) {
        if (e.target === this) closeDeletePermModal();
    });

    modal.addEventListener('click', (e) => { if (e.target === modal) closePermModal(); });
})();
</script>
@endpush
