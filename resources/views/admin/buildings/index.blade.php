@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-5">
    <div class="text-[18px] font-bold text-slate-800">🏢 Manajemen Gedung & Lantai</div>
    <button
        onclick="document.getElementById('modalBuilding').classList.remove('hidden'); document.getElementById('modalBuilding').classList.add('flex');"
        class="bg-[#4f7dfc] hover:bg-[#3a65e0] text-white border-none px-[18px] py-[9px] rounded-lg text-[13px] font-semibold cursor-pointer inline-flex items-center gap-1.5 transition-colors no-underline">
        <i data-feather="plus" class="w-4 h-4"></i> Tambah Gedung
    </button>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-2.5 rounded-lg mb-4 text-[13px]">✓ {{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-[0_1px_4px_rgba(0,0,0,.07)] overflow-hidden mb-5">
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="bg-slate-50 px-4 py-3 text-left text-[12px] font-semibold text-slate-500 border-b border-slate-200">Gedung</th>
                <th class="bg-slate-50 px-4 py-3 text-left text-[12px] font-semibold text-slate-500 border-b border-slate-200">Kode</th>
                <th class="bg-slate-50 px-4 py-3 text-left text-[12px] font-semibold text-slate-500 border-b border-slate-200">Alamat</th>
                <th class="bg-slate-50 px-4 py-3 text-left text-[12px] font-semibold text-slate-500 border-b border-slate-200">Lantai</th>
                <th class="bg-slate-50 px-4 py-3 text-left text-[12px] font-semibold text-slate-500 border-b border-slate-200">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($buildings as $building)
            <tr>
                <td class="px-4 py-3 text-[13px] text-slate-700 border-b border-slate-100">
                    <div class="font-semibold">{{ $building->name }}</div>
                    <div class="text-[11px] text-slate-400">{{ $building->description }}</div>
                </td>
                <td class="px-4 py-3 text-[13px] text-slate-700 border-b border-slate-100">
                    <span class="bg-blue-50 text-blue-700 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">{{ $building->code }}</span>
                </td>
                <td class="px-4 py-3 text-[13px] text-slate-500 border-b border-slate-100">{{ $building->address ?? '-' }}</td>
                <td class="px-4 py-3 text-[13px] text-slate-700 border-b border-slate-100">
                    <div class="flex flex-wrap gap-2">
                        @foreach($building->floors as $floor)
                            <div class="group relative inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 rounded-full pl-3 pr-1 py-1 text-[12px] text-slate-700 transition-colors">
                                <a href="{{ route('admin.floors.show', $floor) }}" class="inline-flex items-center gap-1.5 no-underline text-slate-700">
                                    <i data-feather="layers" class="w-3 h-3"></i>
                                    {{ $floor->name }}
                                </a>
                                <form method="POST" action="{{ route('admin.floors.destroy', $floor) }}"
                                    class="inline-flex" onsubmit="return confirm('Hapus lantai \'{{ $floor->name }}\' beserta semua ruangannya?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        title="Hapus lantai"
                                        class="ml-0.5 w-4 h-4 rounded-full bg-transparent hover:bg-red-500 text-slate-400 hover:text-white border-none cursor-pointer flex items-center justify-center text-[10px] font-bold transition-colors leading-none">
                                        ×
                                    </button>
                                </form>
                            </div>
                        @endforeach
                        <button
                            onclick="openAddFloor({{ $building->id }}, '{{ $building->name }}')"
                            class="inline-flex items-center gap-1.5 border border-dashed border-slate-300 rounded-full px-3 py-1 text-[12px] text-slate-500 bg-transparent cursor-pointer hover:bg-slate-50 transition-colors">
                            <i data-feather="plus" class="w-3 h-3"></i> Lantai
                        </button>
                    </div>
                </td>
                <td class="px-4 py-3 text-[13px] text-slate-700 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <button
                            onclick="openEditBuilding({{ $building->id }}, '{{ $building->name }}', '{{ $building->code }}', '{{ $building->address }}', '{{ $building->description }}')"
                            class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-3 py-1.5 rounded-md text-[12px] cursor-pointer transition-colors">
                            Edit
                        </button>
                        <form method="POST" action="{{ route('admin.buildings.destroy', $building) }}" class="inline-flex" onsubmit="return confirm('Hapus gedung ini?')">
                            @csrf @method('DELETE')
                            <button class="bg-red-500 hover:bg-red-600 text-white border-none px-3 py-1.5 rounded-md text-[12px] cursor-pointer transition-colors">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-slate-400 py-8">Belum ada gedung. Tambahkan gedung pertama!</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal: Tambah Gedung -->
<div class="hidden fixed inset-0 bg-black/40 z-[1000] items-center justify-center" id="modalBuilding">
    <div class="bg-white rounded-xl p-6 w-[480px] max-w-[95vw] shadow-2xl">
        <h3 class="text-[16px] font-bold mb-4">🏢 Tambah Gedung</h3>
        <form method="POST" action="{{ route('admin.buildings.store') }}">
            @csrf
            <div class="mb-3.5">
                <label class="block text-[12px] font-semibold text-slate-500 mb-1.5">Nama Gedung *</label>
                <input type="text" name="name" required placeholder="Gedung A" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-blue-400 box-border">
            </div>
            <div class="mb-3.5">
                <label class="block text-[12px] font-semibold text-slate-500 mb-1.5">Kode Unik *</label>
                <input type="text" name="code" required placeholder="GDG-A" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-blue-400 box-border uppercase">
            </div>
            <div class="mb-3.5">
                <label class="block text-[12px] font-semibold text-slate-500 mb-1.5">Alamat</label>
                <input type="text" name="address" placeholder="Jl. ..." class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-blue-400 box-border">
            </div>
            <div class="mb-3.5">
                <label class="block text-[12px] font-semibold text-slate-500 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="2" placeholder="Keterangan..." class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-blue-400 box-border"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button"
                    onclick="document.getElementById('modalBuilding').classList.add('hidden'); document.getElementById('modalBuilding').classList.remove('flex');"
                    class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-3 py-2 rounded-md text-[12px] cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#4f7dfc] hover:bg-[#3a65e0] text-white border-none px-[18px] py-2 rounded-lg text-[13px] font-semibold cursor-pointer transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Tambah Lantai -->
<div class="hidden fixed inset-0 bg-black/40 z-[1000] items-center justify-center" id="modalFloor">
    <div class="bg-white rounded-xl p-6 w-[480px] max-w-[95vw] shadow-2xl">
        <h3 class="text-[16px] font-bold mb-4">📐 Tambah Lantai</h3>
        <form method="POST" action="{{ route('admin.floors.store') }}">
            @csrf
            <input type="hidden" name="building_id" id="floorBuildingId">
            <div class="mb-3.5">
                <label class="block text-[12px] font-semibold text-slate-500 mb-1.5">Gedung</label>
                <input type="text" id="floorBuildingName" disabled class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] bg-slate-50 box-border">
            </div>
            <div class="mb-3.5">
                <label class="block text-[12px] font-semibold text-slate-500 mb-1.5">Nama Lantai *</label>
                <input type="text" name="name" required placeholder="Lantai 1, Basement, Rooftop..." class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-blue-400 box-border">
            </div>
            <div class="mb-3.5">
                <label class="block text-[12px] font-semibold text-slate-500 mb-1.5">Nomor Lantai *</label>
                <input type="number" name="floor_number" required value="1" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-blue-400 box-border">
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button"
                    onclick="document.getElementById('modalFloor').classList.add('hidden'); document.getElementById('modalFloor').classList.remove('flex');"
                    class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-3 py-2 rounded-md text-[12px] cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#4f7dfc] hover:bg-[#3a65e0] text-white border-none px-[18px] py-2 rounded-lg text-[13px] font-semibold cursor-pointer transition-colors">Buat Lantai</button>
            </div>
        </form>
    </div>
</div>

<script>
feather.replace();
function openAddFloor(buildingId, buildingName) {
    document.getElementById('floorBuildingId').value = buildingId;
    document.getElementById('floorBuildingName').value = buildingName;
    const modal = document.getElementById('modalFloor');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function openEditBuilding(id, name, code, address, description) {
    // TODO: extend with edit modal
}
// Close modals on overlay click
document.querySelectorAll('#modalBuilding, #modalFloor').forEach(overlay => {
    overlay.addEventListener('click', e => {
        if (e.target === overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
    });
});
</script>
@endsection
