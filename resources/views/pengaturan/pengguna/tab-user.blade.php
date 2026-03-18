{{-- Tab: User --}}

{{-- Toolbar --}}
<div class="flex items-center justify-between mb-5">
    <h2 class="text-[16px] font-bold text-slate-800 dark:text-slate-200">Daftar User</h2>
    <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('pengaturan.pengguna') }}" class="flex items-center">
            <input type="hidden" name="tab" value="user">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari user..."
                    class="pl-8 pr-3 py-[7px] border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] rounded-lg text-[12.5px] text-slate-700 focus:outline-none focus:border-red-400 w-52">
            </div>
        </form>
        <button type="button" id="btn-add-user"
            class="flex items-center gap-1.5 bg-red-700 hover:bg-red-800 text-white text-[12.5px] font-semibold px-4 py-[7px] rounded-lg transition-colors cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah User
        </button>
    </div>
</div>

{{-- Table --}}
<div class="rounded-xl border border-slate-200 overflow-hidden dark:border-[#1D1D1D]">
    <table class="w-full text-[13px]">
        <thead class="bg-red-100 border-b border-slate-200 dark:border-[#1D1D1D] dark:bg-[#1D1D1D]">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-slate-800 w-8 dark:text-slate-200">No</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Nama</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Email</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-800 dark:text-slate-200">Role</th>
                <th class="px-5 py-3 text-right font-semibold text-slate-800 dark:text-slate-200">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 dark:divide-[#1D1D1D]">
            @forelse($users as $user)
            <tr class="hover:bg-slate-50/60 dark:hover:bg-transparent transition-colors">
                <td class="px-5 py-3 text-slate-400 dark:text-slate-200">{{ $users->firstItem() + $loop->index }}</td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2.5">
                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ $user->name }}</span>
                        @if($user->id === auth()->id())
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 bg-blue-50 text-blue-500 rounded-full">Anda</span>
                        @endif
                    </div>
                </td>
                <td class="px-5 py-3 text-slate-500 dark:text-slate-200">{{ $user->email }}</td>
                <td class="px-5 py-3">
                    <div class="flex flex-wrap gap-1 ">
                        @forelse($user->roles as $role)
                            <span class="inline-flex items-center rounded-full text-[12px] font-semibold text-slate-700 dark:text-slate-200">
                                {{ $role->name }}
                            </span>
                        @empty
                            <span class="text-slate-400 text-[12px] dark:text-slate-200">—</span>
                        @endforelse
                    </div>
                </td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button class="btn-edit-user text-slate-400 hover:text-blue-600 transition-colors cursor-pointer"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-roles="{{ $user->roles->pluck('name')->implode(',') }}">
                            <img src="{{ asset('icons/edit.svg') }}" alt="Edit" class="w-7 h-7">
                        </button>
                        @if($user->id !== auth()->id())
                        <button onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                class="text-slate-400 hover:text-red-600 transition-colors cursor-pointer">
                            <img src="{{ asset('icons/hapus.svg') }}" alt="Hapus" class="w-7 h-7">
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" class="mx-auto mb-2 text-slate-300"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <p class="text-[13px] font-medium">Belum ada user</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-50 dark:border-[#1D1D1D]">
    <span class="text-[12px] text-slate-400">
        Menampilkan {{ $users->firstItem() ?? 0 }} – {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data
    </span>
    <div class="flex items-center gap-1">
        @if($users->onFirstPage())
            <span class="w-7 h-7 flex items-center justify-center rounded text-slate-300 cursor-not-allowed">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </span>
        @else
            <a href="{{ $users->previousPageUrl() }}&tab=user&search={{ $search }}"
                class="w-7 h-7 flex items-center justify-center rounded text-slate-500 hover:bg-slate-100 no-underline transition-colors">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
        @endif

        @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            <a href="{{ $url }}&tab=user&search={{ $search }}"
                class="w-7 h-7 flex items-center justify-center rounded text-[12px] font-medium no-underline transition-colors
                    {{ $page == $users->currentPage()
                        ? 'bg-red-700 text-white dark:border-[#FDEBEB] dark:bg-[#FDEBEB] dark:text-black'
                        : 'text-slate-500 hover:bg-slate-100' }}">
                {{ $page }}
            </a>
        @endforeach

        @if($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}&tab=user&search={{ $search }}"
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

{{-- ═══════════════ MODAL TAMBAH/EDIT USER ═══════════════ --}}
<div id="modal-user" class="hidden fixed inset-0 bg-black/40 z-[200] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#232323] rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-[#2d2d2d]">
            <h2 id="modal-user-title" class="text-[15px] font-semibold text-slate-800 dark:text-white">Tambah User</h2>
            <button onclick="closeUserModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="form-user" class="px-6 py-5">
            <input type="hidden" id="user-id">

            {{-- Row 1: Nama & Email --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 dark:text-slate-400 mb-1.5">Nama Lengkap</label>
                    <input id="user-name" type="text" placeholder="Nama"
                           class="w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] focus:outline-none focus:border-red-400 transition-colors">
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 dark:text-slate-400 mb-1.5">Email</label>
                    <input id="user-email" type="email" placeholder="email@domain.com"
                           class="w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] focus:outline-none focus:border-red-400 transition-colors">
                </div>
            </div>

            {{-- Row 2: Password & Role --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                        Password <span id="user-pw-hint" class="text-slate-400 font-normal text-[11px]">(isi untuk mengubah)</span>
                    </label>
                    <input id="user-password" type="password" placeholder="Min. 8 karakter"
                           class="w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] focus:outline-none focus:border-red-400 transition-colors">
                </div>
                <div>
                    <label class="block text-[12px] font-medium text-slate-600 dark:text-slate-400 mb-1.5">Role</label>
                    <select id="user-role"
                            class="w-full border border-slate-200 dark:border-[#3d3d3d] dark:bg-[#2a2a2a] dark:text-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] focus:outline-none focus:border-red-400 transition-colors bg-white">
                        <option value="">-- Pilih Role --</option>
                        @foreach($allRoles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="user-error" class="hidden text-[12px] text-red-500 bg-red-50 rounded-lg px-3 py-2 mb-3"></div>
            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100 dark:border-[#2d2d2d]">
                <button type="button" onclick="closeUserModal()"
                        class="px-5 py-2.5 rounded-lg border border-slate-300 dark:border-[#3d3d3d] dark:text-slate-300 dark:hover:bg-[#2a2a2a] text-[13px] font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2.5 bg-red-600 text-white text-[13px] font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast --}}
<div id="toast-user" class="hidden fixed bottom-6 right-6 z-50 text-white text-[13px] font-medium px-5 py-3 rounded-xl shadow-lg"></div>

{{-- Modal Konfirmasi Hapus User --}}
<div id="modal-delete-user" class="hidden fixed inset-0 bg-black/50 z-[300] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#232323] rounded-2xl shadow-2xl w-full max-w-sm text-center overflow-hidden">
        <div class="px-8 pt-8 pb-6">
            {{-- Icon tanpa background --}}
            <div class="flex justify-center mb-4">
                <img src="{{ asset('icons/delete.svg') }}" alt="Hapus" class="w-12 h-12">
            </div>
            {{-- Title --}}
            <h3 class="text-[16px] font-bold text-slate-800 dark:text-white mb-2">Hapus User</h3>
            {{-- Message --}}
            <p class="text-[13px] text-slate-500 dark:text-slate-400">
                Anda yakin ingin menghapus user <strong id="delete-user-name" class="text-slate-700 dark:text-slate-200"></strong>?
                <br>Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        {{-- Footer --}}
        <div class="flex justify-center gap-3 px-8 pb-7">
            <button onclick="closeDeleteUserModal()"
                class="px-7 py-2.5 rounded-lg border border-slate-300 dark:border-[#FFFFFF] dark:text-[#FFFFFF] text-[13px] font-medium text-slate-600 hover:bg-slate-50 dark:hover:bg-[#2a2a2a] transition-colors cursor-pointer bg-white dark:bg-transparent">
                Batal
            </button>
            <button onclick="confirmDeleteUser()"
                class="px-7 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-[13px] font-semibold transition-colors cursor-pointer">
                Hapus
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const modal    = document.getElementById('modal-user');
    const form     = document.getElementById('form-user');
    const errBox   = document.getElementById('user-error');
    const toast    = document.getElementById('toast-user');
    let   editing  = null;

    function showToast(msg, ok = true) {
        toast.textContent = msg;
        toast.className = 'fixed bottom-6 right-6 z-50 text-white text-[13px] font-medium px-5 py-3 rounded-xl shadow-lg '
                        + (ok ? 'bg-green-600' : 'bg-red-600');
        setTimeout(() => toast.className += ' hidden', 3000);
    }

    window.closeUserModal = () => modal.classList.add('hidden');

    document.getElementById('btn-add-user').addEventListener('click', () => {
        editing = null;
        document.getElementById('modal-user-title').textContent = 'Tambah User';
        document.getElementById('user-pw-hint').textContent = '';
        form.reset();
        document.getElementById('user-role').value = '';
        errBox.classList.add('hidden');
        modal.classList.remove('hidden');
    });

    // Edit via data attributes
    document.querySelectorAll('.btn-edit-user').forEach(btn => {
        btn.addEventListener('click', function () {
            const roles = this.dataset.roles ? this.dataset.roles.split(',') : [];
            editing = this.dataset.id;
            document.getElementById('modal-user-title').textContent = 'Edit User';
            document.getElementById('user-pw-hint').textContent = '(isi untuk mengubah)';
            document.getElementById('user-id').value    = this.dataset.id;
            document.getElementById('user-name').value  = this.dataset.name;
            document.getElementById('user-email').value = this.dataset.email;
            document.getElementById('user-password').value = '';
            // Set dropdown to first role found (single select)
            document.getElementById('user-role').value = roles.length > 0 ? roles[0] : '';
            errBox.classList.add('hidden');
            modal.classList.remove('hidden');
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errBox.classList.add('hidden');
        const roleVal = document.getElementById('user-role').value;
        const roles   = roleVal ? [roleVal] : [];
        const body  = {
            name:     document.getElementById('user-name').value,
            email:    document.getElementById('user-email').value,
            password: document.getElementById('user-password').value || undefined,
            roles,
        };
        const url    = editing ? `/pengaturan/pengguna/users/${editing}` : '{{ route('pengaturan.pengguna.users.store') }}';
        const method = editing ? 'PUT' : 'POST';
        const res    = await fetch(url, {
            method,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const json = await res.json();
        if (!res.ok) { errBox.textContent = Object.values(json.errors || {}).flat().join(' '); errBox.classList.remove('hidden'); return; }
        closeUserModal();
        showToast('User berhasil disimpan.');
        setTimeout(() => location.reload(), 700);
    });

    let   deletingId = null;

    window.deleteUser = (id, name) => {
        deletingId = id;
        document.getElementById('delete-user-name').textContent = name;
        document.getElementById('modal-delete-user').classList.remove('hidden');
    };

    window.closeDeleteUserModal = () => {
        deletingId = null;
        document.getElementById('modal-delete-user').classList.add('hidden');
    };

    window.confirmDeleteUser = async () => {
        if (!deletingId) return;
        const res  = await fetch(`/pengaturan/pengguna/users/${deletingId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
        });
        const json = await res.json();
        closeDeleteUserModal();
        if (json.success) { showToast('User dihapus.'); setTimeout(() => location.reload(), 600); }
        else showToast(json.message || 'Gagal menghapus.', false);
    };

    document.getElementById('modal-delete-user').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteUserModal();
    });

    modal.addEventListener('click', (e) => { if (e.target === modal) closeUserModal(); });
})();
</script>
@endpush
