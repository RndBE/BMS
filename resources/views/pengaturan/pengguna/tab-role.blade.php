{{-- Tab: Role --}}

{{-- Toolbar --}}
<div class="flex items-center justify-between mb-5">
    <h2 class="text-[16px] font-bold text-slate-800 dark:text-slate-200">Daftar Role</h2>
    <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('pengaturan.pengguna') }}" class="flex items-center">
            <input type="hidden" name="tab" value="role">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari role..."
                    class="pl-8 pr-3 py-[7px] border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 w-52">
            </div>
        </form>
        <button id="btn-add-role"
            class="flex items-center gap-1.5 bg-red-700 hover:bg-red-800 text-white text-[12.5px] font-semibold px-4 py-[8px] rounded-lg transition-colors cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Role
        </button>
    </div>
</div>

{{-- Table --}}
<div class="rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-[13px]">
        <thead class="bg-red-100 border-b border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a]">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-slate-800 w-8 dark:text-slate-200">No</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Nama Role</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Permissions</th>
                <th class="px-5 py-3 text-right font-semibold text-slate-800 dark:text-slate-200">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
            <tr class="border-b border-slate-100 hover:bg-slate-50/60 dark:hover:bg-transparent transition-colors">
                <td class="px-5 py-3 text-slate-400">{{ $roles->firstItem() + $loop->index }}</td>
                <td class="px-5 py-3">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $role->name }}</span>
                    </span>
                </td>
                <td class="px-5 py-3">
                    <span class="text-[12px] font-medium px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                        {{ $role->permissions_count }} permission
                    </span>
                </td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button class="btn-edit-role p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors"
                                data-id="{{ $role->id }}"
                                data-name="{{ $role->name }}"
                                data-perms="{{ $role->permissions->pluck('name')->implode(',') }}">
                            <img src="{{ asset('icons/edit.svg') }}" alt="Edit" class="w-7 h-7">
                        </button>
                        <button onclick="deleteRole({{ $role->id }})"
                                class="p-1.5 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-500 transition-colors">
                            <img src="{{ asset('icons/hapus.svg') }}" alt="Hapus" class="w-7 h-7">
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                    <img src="{{ asset('icons/empty.svg') }}" alt="Empty" class="w-36 h-36 mx-auto mb-2 text-slate-300">
                    <p class="text-[13px] font-medium">Belum ada role</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($roles->hasPages())
<div class="mt-4">{{ $roles->links() }}</div>
@endif

{{-- ═══════════════ MODAL TAMBAH/EDIT ROLE ═══════════════ --}}
<div id="modal-role" class="hidden fixed inset-0 bg-black/40 z-[200] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#232323] rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4">
            <h2 id="modal-role-title" class="text-[16px] font-bold text-slate-800 dark:text-white">Tambah Role</h2>
            <button onclick="closeRoleModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="form-role" class="px-6 pb-5">
            <input type="hidden" id="role-id">

            {{-- Nama Role --}}
            <div class="mb-4">
                <label class="block text-[12px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Role</label>
                <input id="role-name" type="text" placeholder="e.g. Super Admin"
                       class="w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] focus:outline-none focus:border-red-400 transition-colors">
            </div>

            {{-- Permission Grid --}}
            @if($allPerms->isNotEmpty())
            <div class="mb-4">
                <label class="block text-[12px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Permission</label>
                <div class="max-h-64 overflow-y-auto grid grid-cols-2 gap-2">
                    @foreach($allPerms as $perm)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 border border-slate-200 dark:border-[#3d3d3d] dark:hover:bg-[#2a2a2a] rounded-lg cursor-pointer hover:border-red-300 hover:bg-red-50/30 transition-colors has-[:checked]:border-red-400 has-[:checked]:bg-red-50 dark:has-[:checked]:bg-red-900/20">
                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                               class="role-perm-cb w-4 h-4 accent-red-600 shrink-0">
                        <span class="text-[12.5px] text-slate-700 dark:text-slate-300 truncate">{{ str_replace('_', ' ', $perm->name) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div id="role-error" class="hidden text-[12px] text-red-500 bg-red-50 rounded-lg px-3 py-2 mb-3"></div>
            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-[#2d2d2d]">
                <button type="button" onclick="closeRoleModal()"
                        class="px-6 py-2.5 rounded-lg border border-slate-300 dark:border-[#3d3d3d] dark:text-slate-300 dark:hover:bg-[#2a2a2a] text-[13px] font-medium text-slate-600 hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit"
                        class="px-8 py-2.5 bg-red-700 text-white text-[13px] font-semibold rounded-lg hover:bg-red-800 transition-colors shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Toast --}}
<div id="toast-role" class="hidden fixed bottom-6 right-6 z-50 text-white text-[13px] font-medium px-5 py-3 rounded-xl shadow-lg"></div>

@push('scripts')
<script>
(function () {
    const modal   = document.getElementById('modal-role');
    const form    = document.getElementById('form-role');
    const errBox  = document.getElementById('role-error');
    const toast   = document.getElementById('toast-role');
    let   editing = null;

    function showToast(msg, ok = true) {
        toast.textContent = msg;
        toast.className = 'fixed bottom-6 right-6 z-50 text-white text-[13px] font-medium px-5 py-3 rounded-xl shadow-lg '
                        + (ok ? 'bg-green-600' : 'bg-red-600');
        setTimeout(() => toast.className += ' hidden', 3000);
    }

    window.closeRoleModal = () => modal.classList.add('hidden');

    document.getElementById('btn-add-role').addEventListener('click', () => {
        editing = null;
        document.getElementById('modal-role-title').textContent = 'Tambah Role';
        form.reset();
        document.querySelectorAll('.role-perm-cb').forEach(cb => cb.checked = false);
        errBox.classList.add('hidden');
        modal.classList.remove('hidden');
    });

    // Edit via data attributes
    document.querySelectorAll('.btn-edit-role').forEach(btn => {
        btn.addEventListener('click', function () {
            const perms = this.dataset.perms ? this.dataset.perms.split(',') : [];
            editing = this.dataset.id;
            document.getElementById('modal-role-title').textContent = 'Edit Role';
            document.getElementById('role-id').value   = this.dataset.id;
            document.getElementById('role-name').value = this.dataset.name;
            document.querySelectorAll('.role-perm-cb').forEach(cb => {
                cb.checked = perms.includes(cb.value);
            });
            errBox.classList.add('hidden');
            modal.classList.remove('hidden');
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errBox.classList.add('hidden');
        const perms = [...document.querySelectorAll('.role-perm-cb:checked')].map(cb => cb.value);
        const body  = { name: document.getElementById('role-name').value, permissions: perms };
        const url    = editing ? `/pengaturan/pengguna/roles/${editing}` : '{{ route('pengaturan.pengguna.roles.store') }}';
        const method = editing ? 'PUT' : 'POST';
        const res    = await fetch(url, {
            method,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const json = await res.json();
        if (!res.ok) { errBox.textContent = Object.values(json.errors || {}).flat().join(' '); errBox.classList.remove('hidden'); return; }
        closeRoleModal();
        showToast('Role berhasil disimpan.');
        setTimeout(() => location.reload(), 700);
    });

    window.deleteRole = async (id) => {
        if (!confirm('Hapus role ini?')) return;
        const res  = await fetch(`/pengaturan/pengguna/roles/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
        });
        const json = await res.json();
        if (json.success) { showToast('Role dihapus.'); setTimeout(() => location.reload(), 600); }
    };

    modal.addEventListener('click', (e) => { if (e.target === modal) closeRoleModal(); });
})();
</script>
@endpush
